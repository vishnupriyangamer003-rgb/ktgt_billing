<aside class="sidebar" id="appSidebar">
  <button class="sidebar-collapse-btn d-none d-lg-flex" id="sidebarCollapseBtn" title="Collapse sidebar">
    <i class="bi bi-chevron-left"></i>
  </button>

  <div class="sidebar-brand">
    <div class="brand-mark"><img src="assets/images/logo.png" alt="KTG"></div>
    <div class="brand-text">
      <strong>Knock The Globe</strong>
      <span>Invoice Suite</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Overview</div>
    <a href="index.php" class="side-link" data-nav="dashboard"><i class="bi bi-grid-1x2-fill"></i><span class="link-text">Dashboard</span></a>

    <div class="side-link has-submenu" data-nav="masters">
      <a href="project-types.php"><i class="bi bi-boxes"></i><span class="link-text">Masters</span></a>
      <button class="submenu-caret-btn" title="Show master sections"><i class="bi bi-chevron-right submenu-caret"></i></button>
    </div>
    <div class="side-submenu" id="mastersSubmenu">
      <a href="project-types.php" class="side-sublink">Project Types</a>
      <a href="services.php" class="side-sublink">Services</a>
      <a href="taxes.php" class="side-sublink">Tax Type</a>
      <a href="followup-types.php" class="side-sublink">Follow Up Types</a>
      <a href="servers.php" class="side-sublink">Server Name</a>
      <a href="renewal-types.php" class="side-sublink">Renewal Type</a>
      <a href="paid-status.php" class="side-sublink">Paid Status Type</a>
      <a href="domain-controls.php" class="side-sublink">Domain Control</a>
    </div>

    <div class="nav-section-label">Sales</div>
    <a href="customers.php" class="side-link" data-nav="customers"><i class="bi bi-people-fill"></i><span class="link-text">Customers</span></a>
    <a href="quotation-list.php" class="side-link" data-nav="quotations"><i class="bi bi-file-earmark-text-fill"></i><span class="link-text">Quotations</span></a>
    <a href="invoice-list.php" class="side-link" data-nav="invoices"><i class="bi bi-receipt"></i><span class="link-text">Invoices</span></a>
    <a href="payments.php" class="side-link" data-nav="payments"><i class="bi bi-cash-coin"></i><span class="link-text">Payments</span></a>

    <div class="nav-section-label">Renewals & Hosting</div>
    <a href="renewals.php" class="side-link" data-nav="renewals"><i class="bi bi-arrow-repeat"></i><span class="link-text">Renewals</span></a>

    <div class="nav-section-label">Insights</div>
    <div class="side-link has-submenu" data-nav="reports">
      <a href="reports.php"><i class="bi bi-bar-chart-line-fill"></i><span class="link-text">Reports</span></a>
      <button class="submenu-caret-btn" title="Show report types"><i class="bi bi-chevron-right submenu-caret"></i></button>
    </div>
    <div class="side-submenu" id="reportsSubmenu">
      <a href="reports.php?type=quotation" class="side-sublink">Quotation Report</a>
      <a href="reports.php?type=customer" class="side-sublink">Customer Report</a>
      <a href="reports.php?type=invoice" class="side-sublink">Invoice Report</a>
      <a href="reports.php?type=payment" class="side-sublink">Payment Report</a>
      <a href="reports.php?type=ledger" class="side-sublink">Ledger Report</a>
      <a href="reports.php?type=gst" class="side-sublink">GST Report</a>
      <a href="reports.php?type=nongst" class="side-sublink">Non-GST Report</a>
      <a href="reports.php?type=followup" class="side-sublink">Follow Up Report</a>
    </div>
    <!-- Follow Ups temporarily hidden from navigation — page remains at calendar.php
    <a href="calendar.php" class="side-link" data-nav="calendar"><i class="bi bi-telephone-outbound-fill"></i><span class="link-text">Follow Ups</span><span class="side-badge">3</span></a>
    -->

    <div class="nav-section-label">Account</div>
    <a href="settings.php" class="side-link" data-nav="settings"><i class="bi bi-gear-fill"></i><span class="link-text">Settings</span></a>
    <a href="profile.php" class="side-link" data-nav="profile"><i class="bi bi-person-badge-fill"></i><span class="link-text">Profile</span></a>
    <a href="#" class="side-link js-logout" data-nav="logout"><i class="bi bi-box-arrow-right"></i><span class="link-text">Logout</span></a>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <img src="https://ui-avatars.com/api/?name=Vicky+K&background=2563eb&color=fff&bold=true" alt="user">
      <div class="u-meta">
        <div class="u-name">Vicky</div>
        <div class="u-role">Administrator</div>
      </div>
    </div>
  </div>
</aside>
<div class="backdrop-overlay" id="mobileBackdrop"></div>
