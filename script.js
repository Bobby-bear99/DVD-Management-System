let dvdData = [];
let salesData = [];

document.addEventListener('DOMContentLoaded', function () {
    loadDVDs();
    loadSales();
    initializeNavigation();
    initializeDVDModal();
    initializeSalesModal();
    initializeFilters();
});

function initializeNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    const pages = document.querySelectorAll('.page');

    navItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');

            const pageId = this.getAttribute('data-page');
            pages.forEach(page => page.classList.remove('active'));
            document.getElementById(pageId).classList.add('active');

            if (pageId === 'sales') {
                loadSales();
            }
        });
    });
}

function loadDVDs() {
    fetch('fetch_dvds.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Database Error:", data.error);
                showNotification("Error loading DVDs: " + data.message, 'error');
                return;
            }

            dvdData = data;
            initializeDashboard();
            initializeLibrary();
            initializeManagement();
            populateDVDDropdown();
        })
        .catch(error => {
            console.error('Error loading DVDs:', error);
            showNotification("Failed to load DVD data", 'error');
        });
}

function loadSales(dateFrom = null, dateTo = null) {
    let url = 'fetch_sales.php';
    const params = new URLSearchParams();
    
    if (dateFrom) params.append('dateFrom', dateFrom);
    if (dateTo) params.append('dateTo', dateTo);
    
    if (params.toString()) url += '?' + params.toString();

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Database Error:", data.error);
                showNotification("Error loading sales: " + data.message, 'error');
                return;
            }

            salesData = data;
            renderSalesTable(salesData);
            updateSalesSummary(salesData);
            updateDashboardSales(salesData);
        })
        .catch(error => {
            console.error('Error loading sales:', error);
            showNotification("Failed to load sales data", 'error');
        });
}

function initializeDashboard() {
    const totalDVDs = dvdData.length;
    const lowStockItems = dvdData.filter(dvd => parseInt(dvd.stock) <= 5);
    const lowStockCount = lowStockItems.length;

    document.getElementById('totalDvds').textContent = totalDVDs;
    document.getElementById('lowStock').textContent = lowStockCount;

    const lowStockList = document.getElementById('lowStockList');
    lowStockList.innerHTML = '';

    if (lowStockItems.length === 0) {
        lowStockList.innerHTML = '<p style="text-align:center; color: #999;">No low stock items</p>';
    } else {
        lowStockItems.forEach(dvd => {
            const alertItem = document.createElement('div');
            alertItem.className = 'alert-item';
            alertItem.innerHTML = `<strong>${dvd.title}</strong> - Only ${dvd.stock} left in stock!`;
            lowStockList.appendChild(alertItem);
        });
    }
}

function updateDashboardSales(sales) {
    const today = new Date().toISOString().split('T')[0];
    const todaySales = sales.filter(sale => sale.sale_date === today);
    
    const todayCount = todaySales.length;
    const todayRevenue = todaySales.reduce((sum, sale) => sum + parseFloat(sale.total || 0), 0);

    document.getElementById('todaySales').textContent = todayCount;
    document.getElementById('todayRevenue').textContent = `Rs ${todayRevenue.toFixed(2)}`;

    const recentSalesBody = document.getElementById('recentSalesBody');
    recentSalesBody.innerHTML = '';

    const recentSales = sales.slice(0, 5);
    
    if (recentSales.length === 0) {
        recentSalesBody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No recent sales</td></tr>';
    } else {
        recentSales.forEach(sale => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${sale.dvd_title}</td>
                <td>${sale.quantity}</td>
                <td>Rs ${parseFloat(sale.total).toFixed(2)}</td>
                <td>${sale.sale_date}</td>
            `;
            recentSalesBody.appendChild(row);
        });
    }
}

function initializeLibrary() {
    displayDVDs(dvdData);
}

function displayDVDs(dvds) {
    const container = document.getElementById("dvdContainer");
    container.innerHTML = "";

    if (!dvds || dvds.length === 0) {
        container.innerHTML = "<p style='text-align:center; padding: 2rem; color: #999;'>No DVDs found.</p>";
        return;
    }

    dvds.forEach(dvd => {
        const card = document.createElement("div");
        card.classList.add("dvd-card");

        const imageSrc = dvd.image_path || 'https://via.placeholder.com/220x300?text=No+Image';
        
        card.innerHTML = `
            <div class="dvd-card-image">
                <img src="${imageSrc}" alt="${dvd.title}" onerror="this.src='https://via.placeholder.com/220x300?text=No+Image'">
            </div>
            <div class="dvd-card-content">
                <h3>${dvd.title}</h3>
                <p><strong>Language:</strong> ${dvd.language || 'N/A'}</p>
                <p><strong>Genre:</strong> ${dvd.genre}</p>
                <p><strong>Year:</strong> ${dvd.year}</p>
                <p class="price"><strong>Price:</strong> Rs ${parseFloat(dvd.price).toFixed(2)}</p>
                <p class="${parseInt(dvd.stock) <= 5 ? 'stock-low' : ''}"><strong>Stock:</strong> ${dvd.stock}</p>
            </div>
        `;

        container.appendChild(card);
    });
}

function initializeManagement() {
    renderManagementTable(dvdData);

    const managementSearchInput = document.getElementById("managementSearchInput");
    managementSearchInput.addEventListener("input", () => {
        const query = managementSearchInput.value.toLowerCase().trim();

        const filteredDVDs = dvdData.filter(dvd => 
            dvd.title.toLowerCase().includes(query) ||
            dvd.genre.toLowerCase().includes(query) ||
            dvd.language.toLowerCase().includes(query) ||
            dvd.id.toString().includes(query)
        );

        renderManagementTable(filteredDVDs);
    });
}

function renderManagementTable(dvds) {
    const managementTable = document.getElementById('inventoryTableBody');
    managementTable.innerHTML = '';

    if (!dvds || dvds.length === 0) {
        managementTable.innerHTML = '<tr><td colspan="8" style="text-align:center;">No DVDs found.</td></tr>';
        return;
    }

    dvds.forEach(dvd => {
        const row = document.createElement('tr');
        const stockClass = parseInt(dvd.stock) <= 5 ? 'style="color: red; font-weight: bold;"' : '';
        
        row.innerHTML = `
            <td>${dvd.id}</td>
            <td>${dvd.title}</td>
            <td>${dvd.language}</td>
            <td>${dvd.genre}</td>
            <td>${dvd.year}</td>
            <td>Rs ${parseFloat(dvd.price).toFixed(2)}</td>
            <td ${stockClass}>${dvd.stock}</td>
            <td>
                <button class="btn-edit" onclick="editDVD(${dvd.id})"><i class="fas fa-edit"></i> Edit</button>
                <button class="btn-delete" onclick="deleteDVD(${dvd.id})"><i class="fas fa-trash"></i> Delete</button>
            </td>
        `;
        managementTable.appendChild(row);
    });
}

function initializeDVDModal() {
    const addBtn = document.getElementById("addDvdBtn");
    const modal = document.getElementById("dvdModal");
    const closeBtn = document.getElementById("dvdModalClose");
    const cancelBtn = document.getElementById("cancelDvdBtn");
    const dvdForm = document.getElementById("dvdForm");
    const modalTitle = document.getElementById("modalTitle");
    const dvdIdInput = document.getElementById("dvdId");
    const dvdImageInput = document.getElementById("dvdImage");
    const dvdImagePreview = document.getElementById("dvdImagePreview");

    addBtn.addEventListener("click", () => {
        modalTitle.textContent = "Add New DVD";
        dvdForm.reset();
        dvdIdInput.value = "";
        dvdImagePreview.style.display = "none";
        modal.style.display = "flex";
        modal.style.alignItems = "center";
        modal.style.justifyContent = "center";
    });

    closeBtn.addEventListener("click", () => modal.style.display = "none");
    cancelBtn.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", e => { if(e.target === modal) modal.style.display = "none"; });

    dvdImageInput.addEventListener("change", function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                dvdImagePreview.src = e.target.result;
                dvdImagePreview.style.display = "block";
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    dvdForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(dvdForm);
        const isEdit = dvdIdInput.value !== "";
        const url = isEdit ? "update_dvd.php" : "add_dvd.php";

        const saveBtn = document.getElementById("saveDvdBtn");
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch(url, { method: "POST", body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    showNotification(data.message, 'success');
                    modal.style.display = "none";
                    dvdForm.reset();
                    loadDVDs();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(err => { 
                console.error(err); 
                showNotification("Something went wrong!", 'error');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save';
            });
    });
}

function editDVD(id) {
    const dvd = dvdData.find(d => Number(d.id) === Number(id));
    if (!dvd) {
        showNotification("DVD not found!", 'error');
        return;
    }

    const modal = document.getElementById("dvdModal");
    const modalTitle = document.getElementById("modalTitle");
    const dvdForm = document.getElementById("dvdForm");
    const dvdImagePreview = document.getElementById("dvdImagePreview");

    modalTitle.textContent = "Edit DVD";
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";

    dvdForm.elements["dvdId"].value = dvd.id;
    dvdForm.elements["dvdTitle"].value = dvd.title;
    dvdForm.elements["dvdLanguage"].value = dvd.language;
    dvdForm.elements["dvdGenre"].value = dvd.genre;
    dvdForm.elements["dvdYear"].value = dvd.year;
    dvdForm.elements["dvdPrice"].value = dvd.price;
    dvdForm.elements["dvdStock"].value = dvd.stock;

    if (dvd.image_path) {
        dvdImagePreview.src = dvd.image_path;
        dvdImagePreview.style.display = "block";
    } else {
        dvdImagePreview.style.display = "none";
    }
}

function deleteDVD(id) {
    if (!confirm("Are you sure you want to delete this DVD? This action cannot be undone.")) {
        return;
    }

    fetch(`delete_dvd.php?id=${id}`, { method: "GET" })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification(result.message, 'success');
                loadDVDs();
            } else {
                showNotification(result.message, 'error');
            }
        })
        .catch(error => {
            console.error("Delete error:", error);
            showNotification("Something went wrong while deleting!", 'error');
        });
}

function initializeSalesModal() {
    const addSalesBtn = document.getElementById("addSalesRecordBtn");
    const modal = document.getElementById("salesModal");
    const closeBtn = document.getElementById("salesModalClose");
    const cancelBtn = document.getElementById("cancelSaleBtn");
    const salesForm = document.getElementById("salesForm");
    const dvdSelect = document.getElementById("saleDvdId");
    const priceInput = document.getElementById("salePrice");
    const quantityInput = document.getElementById("saleQuantity");
    const totalInput = document.getElementById("saleTotal");
    const dateInput = document.getElementById("saleDate");
    const stockInfo = document.getElementById("stockInfo");
    const quantityError = document.getElementById("quantityError");

    dateInput.valueAsDate = new Date();

    addSalesBtn.addEventListener("click", () => {
        document.getElementById("salesModalTitle").textContent = "Add Sales Record";
        salesForm.reset();
        document.getElementById("saleId").value = "";
        dateInput.valueAsDate = new Date();
        stockInfo.style.display = "none";
        quantityError.style.display = "none";
        modal.style.display = "flex";
        modal.style.alignItems = "center";
        modal.style.justifyContent = "center";
    });

    closeBtn.addEventListener("click", () => modal.style.display = "none");
    cancelBtn.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", e => { if(e.target === modal) modal.style.display = "none"; });

    dvdSelect.addEventListener("change", function() {
        const selectedId = this.value;
        quantityError.style.display = "none";
        
        if (selectedId) {
            const dvd = dvdData.find(d => d.id == selectedId);
            if (dvd) {
                priceInput.value = parseFloat(dvd.price).toFixed(2);
                stockInfo.textContent = `Available stock: ${dvd.stock}`;
                stockInfo.style.display = "block";
                stockInfo.style.color = parseInt(dvd.stock) <= 5 ? 'red' : '#666';
                calculateTotal();
            }
        } else {
            priceInput.value = "";
            totalInput.value = "";
            stockInfo.style.display = "none";
        }
    });

    quantityInput.addEventListener("input", function() {
        quantityError.style.display = "none";
        const selectedId = dvdSelect.value;
        
        if (selectedId) {
            const dvd = dvdData.find(d => d.id == selectedId);
            if (dvd && parseInt(this.value) > parseInt(dvd.stock)) {
                quantityError.textContent = `Only ${dvd.stock} available in stock!`;
                quantityError.style.display = "block";
            }
        }
        calculateTotal();
    });

    function calculateTotal() {
        const price = parseFloat(priceInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        totalInput.value = (price * quantity).toFixed(2);
    }

    salesForm.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(salesForm);
        const saleId = document.getElementById("saleId").value;
        const isEdit = saleId !== "";
        const url = isEdit ? "update_sales.php" : "add_sales.php";

        const saveBtn = document.getElementById("saveSaleBtn");
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch(url, { method: "POST", body: formData })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification(result.message, 'success');
                    modal.style.display = "none";
                    salesForm.reset();
                    loadSales();
                    loadDVDs();
                } else {
                    showNotification(result.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification("Something went wrong!", 'error');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save';
            });
    });

    const filterBtn = document.getElementById("filterSales");
    filterBtn.addEventListener("click", () => {
        const dateFrom = document.getElementById("dateFrom").value;
        const dateTo = document.getElementById("dateTo").value;
        loadSales(dateFrom, dateTo);
    });
}

function populateDVDDropdown() {
    const dvdSelect = document.getElementById("saleDvdId");
    dvdSelect.innerHTML = '<option value="">Select DVD</option>';
    
    dvdData.forEach(dvd => {
        const option = document.createElement('option');
        option.value = dvd.id;
        option.textContent = `${dvd.title} - Rs ${parseFloat(dvd.price).toFixed(2)} (Stock: ${dvd.stock})`;
        if (parseInt(dvd.stock) === 0) {
            option.disabled = true;
            option.textContent += ' - OUT OF STOCK';
        }
        dvdSelect.appendChild(option);
    });
}

function renderSalesTable(sales) {
    const salesTableBody = document.getElementById('salesTableBody');
    salesTableBody.innerHTML = '';

    if (!sales || sales.length === 0) {
        salesTableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No sales records found</td></tr>';
        return;
    }

    sales.forEach(sale => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${sale.id}</td>
            <td>${sale.sale_date}</td>
            <td>${sale.dvd_title}</td>
            <td>${sale.quantity}</td>
            <td>Rs ${parseFloat(sale.price).toFixed(2)}</td>
            <td>Rs ${parseFloat(sale.total).toFixed(2)}</td>
            <td>
                <button class="btn-edit" onclick="editSale(${sale.id})"><i class="fas fa-edit"></i></button>
                <button class="btn-delete" onclick="deleteSale(${sale.id})"><i class="fas fa-trash"></i></button>
            </td>
        `;
        salesTableBody.appendChild(row);
    });
}

function updateSalesSummary(sales) {
    const totalTransactions = sales.length;
    const totalRevenue = sales.reduce((sum, sale) => sum + parseFloat(sale.total || 0), 0);
    const averageSale = totalTransactions > 0 ? totalRevenue / totalTransactions : 0;

    document.getElementById('totalTransactions').textContent = totalTransactions;
    document.getElementById('totalRevenue').textContent = `Rs ${totalRevenue.toFixed(2)}`;
    document.getElementById('averageSale').textContent = `Rs ${averageSale.toFixed(2)}`;
}

function editSale(id) {
    const sale = salesData.find(s => Number(s.id) === Number(id));
    if (!sale) {
        showNotification("Sale record not found!", 'error');
        return;
    }

    const modal = document.getElementById("salesModal");
    const modalTitle = document.getElementById("salesModalTitle");
    const salesForm = document.getElementById("salesForm");

    modalTitle.textContent = "Edit Sales Record";
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";

    salesForm.elements["saleId"].value = sale.id;
    salesForm.elements["dvdId"].value = sale.dvd_id;
    salesForm.elements["price"].value = parseFloat(sale.price).toFixed(2);
    salesForm.elements["quantity"].value = sale.quantity;
    salesForm.elements["total"].value = parseFloat(sale.total).toFixed(2);
    salesForm.elements["saleDate"].value = sale.sale_date;

    const dvd = dvdData.find(d => d.id == sale.dvd_id);
    if (dvd) {
        const stockInfo = document.getElementById("stockInfo");
        stockInfo.textContent = `Available stock: ${dvd.stock}`;
        stockInfo.style.display = "block";
    }
}

function deleteSale(id) {
    if (!confirm("Are you sure you want to delete this sale record? Stock will be restored.")) {
        return;
    }

    fetch(`delete_sales.php?id=${id}`, { method: "GET" })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification(result.message, 'success');
                loadSales();
                loadDVDs();
            } else {
                showNotification(result.message, 'error');
            }
        })
        .catch(error => {
            console.error("Delete error:", error);
            showNotification("Something went wrong while deleting!", 'error');
        });
}

function initializeFilters() {
    const librarySearchInput = document.getElementById("searchInput");
    const categoryFilter = document.getElementById("categoryFilter");
    const languageFilter = document.getElementById("languageFilter");
    const genreFilter = document.getElementById("genreFilter");
    const yearFilter = document.getElementById("yearFilter");

    function filterLibrary() {
        const query = librarySearchInput.value.toLowerCase().trim();
        const category = categoryFilter.value;
        const language = languageFilter.value;
        const genre = genreFilter.value;
        const year = yearFilter.value;

        const filteredDVDs = dvdData.filter(dvd => {
            const matchesSearch = dvd.title.toLowerCase().includes(query) ||
                                 dvd.genre.toLowerCase().includes(query) ||
                                 dvd.language.toLowerCase().includes(query);
            const matchesLanguage = language === "all" || dvd.language.toLowerCase() === language;
            const matchesGenre = genre === "all" || dvd.genre.toLowerCase() === genre;

            let matchesYear = true;
            if (year !== "all") {
                if (year === "classic") {
                    matchesYear = parseInt(dvd.year) < 2020;
                } else if (year.endsWith("s")) {
                    matchesYear = parseInt(dvd.year) === parseInt(year.slice(0, 4));
                }
            }

            return matchesSearch && matchesLanguage && matchesGenre && matchesYear;
        });

        displayDVDs(filteredDVDs);
    }

    librarySearchInput.addEventListener("input", filterLibrary);
    categoryFilter.addEventListener("change", filterLibrary);
    languageFilter.addEventListener("change", filterLibrary);
    genreFilter.addEventListener("change", filterLibrary);
    yearFilter.addEventListener("change", filterLibrary);
}

function showNotification(message, type = 'success') {
    let notif = document.createElement('div');
    notif.className = `notification ${type}`;
    notif.textContent = message;
    document.body.appendChild(notif);

    setTimeout(() => {
        notif.classList.add('show');
    }, 100);

    setTimeout(() => {
        notif.classList.remove('show');
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}
