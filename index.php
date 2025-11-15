<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DVD Shop Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <i class="fas fa-compact-disc"></i>
                <h2>DVD Shop</h2>
            </div>
            <nav class="nav-menu">
                <a href="#" class="nav-item active" data-page="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-item" data-page="library">
                    <i class="fas fa-film"></i>
                    <span>Library</span>
                </a>
                <a href="#" class="nav-item" data-page="sales">
                    <i class="fas fa-chart-line"></i>
                    <span>Sales Records</span>
                </a>
                <a href="#" class="nav-item" data-page="management">
                    <i class="fas fa-cog"></i>
                    <span>Management</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <div id="dashboard" class="page active">
                <div class="page-header">
                    <h1>Dashboard</h1>
                    <p class="subtitle">Overview of your DVD shop</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-compact-disc"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="totalDvds">0</h3>
                            <p>Total DVDs</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="todayRevenue">Rs 0</h3>
                            <p>Today's Revenue</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="todaySales">0</h3>
                            <p>Sales Today</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="lowStock">0</h3>
                            <p>Low Stock Items</p>
                        </div>
                    </div>
                </div>
                <div class="dashboard-sections">
                    <div class="section">
                        <h2>Recent Sales</h2>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>DVD Title</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="recentSalesBody">
                                    <tr><td colspan="4" style="text-align:center;">No recent sales</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="section">
                        <h2>Low Stock Alert</h2>
                        <div class="alert-list" id="lowStockList">
                            <p style="text-align:center; color: #999;">No low stock items</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="library" class="page">
                <div class="page-header">
                    <h1>DVD Library</h1>
                    <p class="subtitle">Browse and search your DVD collection</p>
                </div>
                <div class="library-search-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search DVDs...">
                    </div>
                    <div class="library-filters">
                        <select id="categoryFilter">
                            <option value="all">All Categories</option>
                            <option value="new">New Releases</option>
                            <option value="popular">Popular</option>
                            <option value="classic">Classic</option>
                        </select>
                        <select id="languageFilter">
                            <option value="all">All Languages</option>
                            <option value="english">English</option>
                            <option value="sinhala">Sinhala</option>
                            <option value="tamil">Tamil</option>
                            <option value="korean">Korean</option>
                            <option value="kannada">Kannada</option>
                            <option value="japanese">Japanese</option>
                        </select>
                        <select id="genreFilter">
                            <option value="all">All Genres</option>
                            <option value="action">Action</option>
                            <option value="comedy">Comedy</option>
                            <option value="drama">Drama</option>
                            <option value="horror">Horror</option>
                            <option value="scifi">Sci-Fi</option>
                            <option value="romance">Romance</option>
                        </select>
                        <select id="yearFilter">
                            <option value="all">All Years</option>
                            <option value="2025s">2025</option>
                            <option value="2024s">2024</option>
                            <option value="2023s">2023</option>
                            <option value="2022s">2022</option>
                            <option value="2021s">2021</option>
                            <option value="2020s">2020</option>
                            <option value="classic">Before 2020</option>
                        </select>
                    </div>
                </div>
                <div id="dvdContainer" class="dvd-grid"></div>
            </div>

            <div id="sales" class="page">
                <div class="page-header">
                    <h1>Sales Records</h1>
                    <p class="subtitle">View and manage sales transactions</p>
                </div>

                <div class="sales-summary">
                    <div class="summary-card">
                        <h3 id="totalTransactions">0</h3>
                        <p>Total Transactions</p>
                    </div>
                    <div class="summary-card">
                        <h3 id="totalRevenue">Rs 0</h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="summary-card">
                        <h3 id="averageSale">Rs 0</h3>
                        <p>Average Sale</p>
                    </div>
                </div>

                <div class="sales-controls">
                    <div class="date-filter">
                        <button class="btn-primary" id="addSalesRecordBtn">
                            <i class="fas fa-plus"></i> Add Sales Record
                        </button>
                        <label>From:</label>
                        <input type="date" id="dateFrom">
                        <label>To:</label>
                        <input type="date" id="dateTo">
                        <button class="btn-primary" id="filterSales">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>                    
                </div>
                
                <div class="table-container">
                    <table id="salesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>DVD Title</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                            <tr><td colspan="7" style="text-align:center;">No sales records found</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="management" class="page">
                <div class="page-header">
                    <h1>Inventory Management</h1>
                    <p class="subtitle">Add, edit, and manage your DVD inventory</p>
                </div>
                <div class="management-controls">
                    <button class="btn-primary" id="addDvdBtn">
                        <i class="fas fa-plus"></i> Add New DVD
                    </button>
                </div>
                <div class="management-search">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="managementSearchInput" placeholder="Search inventory by title, genre, or ID...">
                    </div>
                </div>
                <div class="table-container">
                    <table id="inventoryTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Language</th>
                                <th>Genre</th>
                                <th>Year</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            <tr><td colspan="8" style="text-align:center;">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- DVD Modal (Add / Edit) -->
    <div id="dvdModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New DVD</h2>
                <span class="close" id="dvdModalClose">&times;</span>
            </div>
            <form id="dvdForm" enctype="multipart/form-data">
                <input type="hidden" name="dvdId" id="dvdId">
                
                <div class="form-group">
                    <label for="dvdTitleInput">Title <span class="required">*</span></label>
                    <input type="text" id="dvdTitleInput" name="dvdTitle" required placeholder="Enter DVD title">
                </div>
                <div class="form-group">
                    <label for="dvdLanguage">Language <span class="required">*</span></label>
                    <select id="dvdLanguage" name="dvdLanguage" required>
                        <option value="">Select Language</option>
                        <option value="english">English</option>
                        <option value="sinhala">Sinhala</option>
                        <option value="tamil">Tamil</option>
                        <option value="korean">Korean</option>
                        <option value="kannada">Kannada</option>
                        <option value="japanese">Japanese</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dvdGenre">Genre <span class="required">*</span></label>
                    <select id="dvdGenre" name="dvdGenre" required>
                        <option value="">Select Genre</option>
                        <option value="action">Action</option>
                        <option value="comedy">Comedy</option>
                        <option value="drama">Drama</option>
                        <option value="horror">Horror</option>
                        <option value="scifi">Sci-Fi</option>
                        <option value="romance">Romance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dvdYear">Year <span class="required">*</span></label>
                    <input type="number" id="dvdYear" name="dvdYear" min="1900" max="2025" required placeholder="e.g., 2024">
                </div>
                <div class="form-group">
                    <label for="dvdPrice">Price (Rs) <span class="required">*</span></label>
                    <input type="number" id="dvdPrice" name="dvdPrice" step="0.01" min="0" required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="dvdStock">Stock Quantity <span class="required">*</span></label>
                    <input type="number" id="dvdStock" name="dvdStock" min="0" required placeholder="0">
                </div>
                <div class="form-group">
                    <label for="dvdImage">DVD Cover Image</label>
                    <input type="file" id="dvdImage" name="dvdImage" accept="image/*">
                    <img id="dvdImagePreview" src="" style="max-width: 200px; display:none; margin-top:10px; border-radius: 8px;">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelDvdBtn">Cancel</button>
                    <button type="submit" class="btn-primary" id="saveDvdBtn">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Record Modal (Add / Edit) -->
    <div id="salesModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="salesModalTitle">Add Sales Record</h2>
                <span class="close" id="salesModalClose">&times;</span>
            </div>
            <form id="salesForm">
                <input type="hidden" name="saleId" id="saleId">
                
                <div class="form-group">
                    <label for="saleDvdId">DVD Title <span class="required">*</span></label>
                    <select id="saleDvdId" name="dvdId" required>
                        <option value="">Select DVD</option>
                    </select>
                    <small id="stockInfo" style="color: #666; display: none;"></small>
                </div>
                
                <div class="form-group">
                    <label for="salePrice">Price (Rs) <span class="required">*</span></label>
                    <input type="number" id="salePrice" name="price" step="0.01" min="0" readonly required>
                </div>
                
                <div class="form-group">
                    <label for="saleQuantity">Quantity <span class="required">*</span></label>
                    <input type="number" id="saleQuantity" name="quantity" min="1" required placeholder="1">
                    <small id="quantityError" style="color: red; display: none;"></small>
                </div>
                
                <div class="form-group">
                    <label for="saleTotal">Total (Rs)</label>
                    <input type="number" id="saleTotal" name="total" step="0.01" readonly required>
                </div>
                
                <div class="form-group">
                    <label for="saleDate">Sale Date <span class="required">*</span></label>
                    <input type="date" id="saleDate" name="saleDate" required>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelSaleBtn">Cancel</button>
                    <button type="submit" class="btn-primary" id="saveSaleBtn">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
