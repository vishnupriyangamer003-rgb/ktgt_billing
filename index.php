<?php
$pageTitle = "Dashboard";
$pageKey = "dashboard";
include __DIR__ . '/components/header.php';
?>
      <div class="page-head fade-in-up">
        <div>
          <h1>Welcome back, Vicky 👋</h1>
          <p class="text-muted mb-0 fs-13">Here's what's happening with your business today, Tuesday 04 Aug 2026.</p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-light"><i class="bi bi-download me-1"></i>Export Report</button>
          <a href="quotation-create.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Quotation</a>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="row g-3 stagger mb-3">
        <div class="col-6 col-md-4 col-xl-3">
          <div class="stat-card hover-lift">
            <div class="stat-icon bg-soft-primary"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">Total Customers</div>
            <div class="stat-value">128</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>8.2% this month</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="stat-card hover-lift">
            <div class="stat-icon bg-soft-purple"><i class="bi bi-kanban-fill"></i></div>
            <div class="stat-label">Total Projects</div>
            <div class="stat-value">64</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>4.1% this month</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="stat-card hover-lift">
            <div class="stat-icon bg-soft-warning"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-label">Pending Quotations</div>
            <div class="stat-value">14</div>
            <div class="stat-trend down"><i class="bi bi-arrow-down-short"></i>2.4% this month</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="stat-card hover-lift">
            <div class="stat-icon bg-soft-success"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">Approved Quotations</div>
            <div class="stat-value">46</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>11% this month</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="stat-card hover-lift">
            <div class="stat-icon bg-soft-info"><i class="bi bi-receipt"></i></div>
            <div class="stat-label">Total Invoices</div>
            <div class="stat-value">212</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>6.7% this month</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="stat-card hover-lift">
            <div class="stat-icon bg-soft-danger"><i class="bi bi-exclamation-circle-fill"></i></div>
            <div class="stat-label">Pending Payments</div>
            <div class="stat-value">9</div>
            <div class="stat-trend down"><i class="bi bi-arrow-down-short"></i>1.2% this month</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="stat-card hover-lift">
            <div class="stat-icon bg-soft-primary"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">₹48.6L</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>9.8% YTD</div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="stat-card hover-lift">
            <div class="stat-icon bg-soft-success"><i class="bi bi-calendar2-check-fill"></i></div>
            <div class="stat-label">Monthly Revenue</div>
            <div class="stat-value">₹5.12L</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i>12.4% vs last month</div>
          </div>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="row g-3 mb-3">
        <div class="col-xl-8">
          <div class="section-card h-100 fade-in-up">
            <div class="section-head">
              <div>
                <h5>Monthly Sales</h5>
                <div class="subtitle">Revenue trend for the current financial year</div>
              </div>
              <div class="btn-group">
                <button class="btn btn-sm btn-soft-primary">Monthly</button>
                <button class="btn btn-sm btn-light">Quarterly</button>
                <button class="btn btn-sm btn-light">Yearly</button>
              </div>
            </div>
            <div class="section-body">
              <div id="monthlySalesChart"></div>
            </div>
          </div>
        </div>
        <div class="col-xl-4">
          <div class="section-card h-100 fade-in-up">
            <div class="section-head">
              <div><h5>Quotation Status</h5><div class="subtitle">Overall distribution</div></div>
            </div>
            <div class="section-body d-flex flex-column align-items-center">
              <div id="quotationDonut"></div>
              <div class="chart-legend justify-content-center" id="quotationLegend"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="row g-3 mb-3">
        <div class="col-xl-4">
          <div class="section-card h-100 fade-in-up">
            <div class="section-head"><div><h5>Invoice Status</h5><div class="subtitle">Current invoice book</div></div></div>
            <div class="section-body d-flex flex-column align-items-center">
              <div id="invoiceDonut"></div>
              <div class="chart-legend justify-content-center" id="invoiceLegend"></div>
            </div>
          </div>
        </div>
        <div class="col-xl-8">
          <div class="section-card h-100 fade-in-up">
            <div class="section-head">
              <div><h5>Payment Collection</h5><div class="subtitle">Collected vs Target (last 6 months)</div></div>
              <div class="chart-legend mb-0">
                <span class="leg-item"><span class="leg-dot" style="background:var(--primary)"></span>Collected</span>
                <span class="leg-item"><span class="leg-dot" style="background:var(--border)"></span>Target</span>
              </div>
            </div>
            <div class="section-body">
              <div id="paymentCollectionChart"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Lists Row -->
      <div class="row g-3 mb-3">
        <div class="col-xl-4">
          <div class="section-card h-100 fade-in-up">
            <div class="section-head"><h5>Recent Activities</h5><a href="reports.php" class="fs-12 fw-600 text-primary-KTG">View all</a></div>
            <div class="section-body">
              <div class="timeline" id="activityTimeline"></div>
            </div>
          </div>
        </div>

        <div class="col-xl-4">
          <div class="section-card h-100 fade-in-up">
            <div class="section-head"><h5>Upcoming Expiry</h5><span class="chip chip-pending">4 items</span></div>
            <div class="section-body p-0">
              <div class="list-group list-group-flush">
                <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3">
                  <div class="avatar-sm bg-soft-warning" style="color:var(--warning)"><i class="bi bi-globe2"></i></div>
                  <div class="flex-grow-1">
                    <div class="cell-title fs-13">brightretail.in</div>
                    <div class="cell-sub">Domain renewal · Bright Retail Solutions</div>
                  </div>
                  <span class="chip chip-pending">5 days</span>
                </div>
                <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3">
                  <div class="avatar-sm bg-soft-info" style="color:var(--info)"><i class="bi bi-hdd-network"></i></div>
                  <div class="flex-grow-1">
                    <div class="cell-title fs-13">zenfoods.com hosting</div>
                    <div class="cell-sub">Hosting renewal · Zen Foods Pvt Ltd</div>
                  </div>
                  <span class="chip chip-pending">9 days</span>
                </div>
                <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3">
                  <div class="avatar-sm bg-soft-danger" style="color:var(--danger)"><i class="bi bi-file-earmark-excel"></i></div>
                  <div class="flex-grow-1">
                    <div class="cell-title fs-13">QUO-2026-0092</div>
                    <div class="cell-sub">Quotation expiry · Nova Textiles</div>
                  </div>
                  <span class="chip chip-rejected">Tomorrow</span>
                </div>
                <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3">
                  <div class="avatar-sm bg-soft-primary"><i class="bi bi-receipt"></i></div>
                  <div class="flex-grow-1">
                    <div class="cell-title fs-13">INV-2026-0150</div>
                    <div class="cell-sub">Invoice due · Zen Foods Pvt Ltd</div>
                  </div>
                  <span class="chip chip-pending">2 days</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-4">
          <div class="section-card h-100 fade-in-up">
            <div class="section-head"><h5>Pending Follow Ups</h5><a href="calendar.php" class="fs-12 fw-600 text-primary-KTG">View all</a></div>
            <div class="section-body p-0">
              <div class="list-group list-group-flush" id="dashFollowups"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Quotations Table -->
      <div class="section-card fade-in-up">
        <div class="section-head">
          <div><h5>Recent Quotations</h5><div class="subtitle">Latest quotations across all customers</div></div>
          <a href="quotation-list.php" class="btn btn-sm btn-light">View All Quotations</a>
        </div>
        <div class="table-responsive">
          <table class="table table-modern mb-0">
            <thead>
              <tr><th>Quotation #</th><th>Customer</th><th>Project</th><th>Date</th><th>Amount</th><th>Status</th><th></th></tr>
            </thead>
            <tbody id="dashQuotationsBody"></tbody>
          </table>
        </div>
      </div>

<?php
$pageScript = <<<'JS'
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(renderDashboard, 150); // allow layout components to load
});

function renderDashboard(){
  const D = KTG.DATA;

  // Monthly Sales line chart (first 8 months with data)
  const salesData = D.salesMonthly.filter(m => m.v > 0).map(m => ({label: m.m, value: m.v}));
  KTG.Charts.line(document.getElementById('monthlySalesChart'), salesData, {height:220, valueFormatter: KTG.moneyShort});

  // Quotation donut
  KTG.Charts.donut(document.getElementById('quotationDonut'), D.quotationStatus, {centerLabel:'Quotations'});
  document.getElementById('quotationLegend').innerHTML = D.quotationStatus.map(d =>
    `<span class="leg-item"><span class="leg-dot" style="background:${d.color}"></span>${d.label} (${d.value})</span>`).join('');

  // Invoice donut
  KTG.Charts.donut(document.getElementById('invoiceDonut'), D.invoiceStatus, {centerLabel:'Invoices'});
  document.getElementById('invoiceLegend').innerHTML = D.invoiceStatus.map(d =>
    `<span class="leg-item"><span class="leg-dot" style="background:${d.color}"></span>${d.label} (${d.value})</span>`).join('');

  // Payment collection grouped bar
  KTG.Charts.groupedBar(document.getElementById('paymentCollectionChart'), D.paymentCollection);

  // Activity timeline
  const dotColors = {success:'var(--success)', primary:'var(--primary)', info:'var(--info)', purple:'var(--purple)', warning:'var(--warning)'};
  document.getElementById('activityTimeline').innerHTML = D.activities.map(a => `
    <div class="timeline-item">
      <div class="t-dot" style="color:${dotColors[a.color]}"><i class="bi ${a.icon}"></i></div>
      <div class="t-time">${a.time}</div>
      <div class="t-title" style="font-weight:500;">${a.text}</div>
    </div>`).join('');

  // Follow ups
  const prIcon = {high:'priority-high', medium:'priority-medium', low:'priority-low'};
  const typeIcon = {call:'bi-telephone-fill', meeting:'bi-camera-video-fill', email:'bi-envelope-fill', whatsapp:'bi-whatsapp'};
  document.getElementById('dashFollowups').innerHTML = D.followups.filter(f=>f.status==='pending').slice(0,5).map(f => `
    <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3">
      <div class="avatar-sm bg-soft-primary"><i class="bi ${typeIcon[f.type]}"></i></div>
      <div class="flex-grow-1">
        <div class="cell-title fs-13"><span class="priority-dot ${prIcon[f.priority]}"></span>${f.customer}</div>
        <div class="cell-sub">${f.subject}</div>
      </div>
      <div class="text-end">
        <div class="fs-11 fw-600 text-muted">${f.next}</div>
      </div>
    </div>`).join('');

  // Recent quotations table
  document.getElementById('dashQuotationsBody').innerHTML = D.quotations.slice(0,6).map(q => `
    <tr>
      <td class="cell-title">${q.id}</td>
      <td>${q.customer}</td>
      <td>${q.project}</td>
      <td>${q.date}</td>
      <td class="fw-600">${KTG.money(q.amount)}</td>
      <td><span class="chip chip-${q.status}">${q.status}</span></td>
      <td><a href="quotation-create.php" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a></td>
    </tr>`).join('');
  KTG.makeResponsiveTable(document.getElementById('dashQuotationsBody').closest('table'));
}
JS;
?>
<?php include __DIR__ . '/components/footer.php'; ?>
