/* ============================================================
   KTG Invoice & Quotation Manager — app.js
   Shared layout loader, dummy data, auth, masters, notifications,
   ledger, chart engine, UI helpers
   ============================================================ */

/* ---------------- Auth Guard (runs immediately, before paint) ---------------- */
(function authGuard() {
  const page = location.pathname.split('/').pop() || 'index.php';
  const PUBLIC_PAGES = ['login.php', ''];
  const loggedIn = sessionStorage.getItem('ktg_auth') === '1';
  if (!loggedIn && !PUBLIC_PAGES.includes(page)) {
    window.location.replace('login.php');
  }
})();

const KTG = (() => {

  /* ---------------- Dummy Data Store ---------------- */
  const DATA = {
    company: {
      name: "Knock The Globe Technologies",
      short: "KTG",
      address: "4th Cross Rd, Vaiyapuri Nagar, Karur, Tamil Nadu 639002",
      phone: "72001 23452",
      email: "knockus@knocktheglobe.com",
      website: "www.knocktheglobe.com",
      gst: "33ABCDE1234F1Z5"
    },
    stats: {
      customers: 128, customersUp: 8.2,
      projects: 64, projectsUp: 4.1,
      pendingQuotations: 14, approvedQuotations: 46,
      invoices: 212, pendingPayments: 9,
      totalRevenue: 4860000, monthlyRevenue: 512000, monthlyUp: 12.4
    },
    salesMonthly: [
      {m:"Jan", v: 320000},{m:"Feb", v: 280000},{m:"Mar", v: 410000},{m:"Apr", v: 360000},
      {m:"May", v: 450000},{m:"Jun", v: 380000},{m:"Jul", v: 470000},{m:"Aug", v: 512000},
      {m:"Sep", v: 0},{m:"Oct", v: 0},{m:"Nov", v: 0},{m:"Dec", v: 0}
    ],
    quotationStatus: [
      {label:"Approved", value: 46, color:"#16a34a"},
      {label:"Sent", value: 22, color:"#0891b2"},
      {label:"Pending", value: 14, color:"#d97706"},
      {label:"Rejected", value: 8, color:"#dc2626"},
      {label:"Expired", value: 6, color:"#64748b"}
    ],
    invoiceStatus: [
      {label:"Paid", value: 132, color:"#16a34a"},
      {label:"Pending", value: 44, color:"#d97706"},
      {label:"Partially Paid", value: 21, color:"#2563eb"},
      {label:"Overdue", value: 15, color:"#dc2626"}
    ],
    paymentCollection: [
      {m:"Mar", collected: 340000, target: 400000},
      {m:"Apr", collected: 310000, target: 380000},
      {m:"May", collected: 420000, target: 430000},
      {m:"Jun", collected: 355000, target: 400000},
      {m:"Jul", collected: 460000, target: 450000},
      {m:"Aug", collected: 480000, target: 500000}
    ],
    customers: [
      {id:1, name:"Zen Foods Pvt Ltd", company:"Zen Foods Pvt Ltd", contact:"Arun Kumar", mobile:"98765 43210", whatsapp:"98765 43210", email:"arun@zenfoods.com", gst:"33ZENFD1234F1Z2", address:"12 Anna Nagar, Karur, TN", status:"active", type:"Website", created:"2026-01-14", city:"Karur"},
      {id:2, name:"Bright Retail Solutions", company:"Bright Retail Solutions", contact:"Priya S", mobile:"98432 11223", whatsapp:"98432 11223", email:"priya@brightretail.in", gst:"33BRSPL5678F1Z9", address:"45 Gandhi Rd, Trichy, TN", status:"active", type:"Web Application", created:"2025-11-02", city:"Trichy"},
      {id:3, name:"Nova Textiles", company:"Nova Textiles Ltd", contact:"Karthik R", mobile:"90000 12345", whatsapp:"90000 12345", email:"karthik@novatextiles.com", gst:"33NOVAT9988F1Z3", address:"7 Mill Street, Coimbatore, TN", status:"pending", type:"Website", created:"2026-02-20", city:"Coimbatore"},
      {id:4, name:"Sunrise Hospitals", company:"Sunrise Hospitals", contact:"Dr. Meena", mobile:"99887 66554", whatsapp:"99887 66554", email:"meena@sunrisehosp.com", gst:"33SUNH2233F1Z6", address:"22 Hospital Rd, Madurai, TN", status:"active", type:"Web Application", created:"2025-09-18", city:"Madurai"},
      {id:5, name:"Velocity Motors", company:"Velocity Motors Pvt Ltd", contact:"Suresh Babu", mobile:"93445 12009", whatsapp:"93445 12009", email:"suresh@velocitymotors.in", gst:"33VELM4455F1Z1", address:"9 Bypass Road, Salem, TN", status:"inactive", type:"Website", created:"2025-06-10", city:"Salem"},
      {id:6, name:"Orbit Learning Academy", company:"Orbit Learning Academy", contact:"Divya N", mobile:"90032 11987", whatsapp:"90032 11987", email:"divya@orbitlearning.edu", gst:"33ORBL7788F1Z4", address:"18 School St, Erode, TN", status:"active", type:"Web Application", created:"2026-03-05", city:"Erode"},
      {id:7, name:"Green Leaf Organics", company:"Green Leaf Organics", contact:"Ramesh V", mobile:"91234 56780", whatsapp:"91234 56780", email:"ramesh@greenleaf.com", gst:"33GRNLF3344F1Z8", address:"3 Market Rd, Karur, TN", status:"active", type:"Website", created:"2026-04-11", city:"Karur"},
      {id:8, name:"Pinnacle Constructions", company:"Pinnacle Constructions", contact:"Vijay Anand", mobile:"98123 45670", whatsapp:"98123 45670", email:"vijay@pinnacleco.in", gst:"33PINC6677F1Z0", address:"55 Ring Rd, Trichy, TN", status:"pending", type:"Web Application", created:"2026-05-22", city:"Trichy"}
    ],
    quotations: [
      {id:"QUO-2026-0091", customer:"Zen Foods Pvt Ltd", project:"Corporate Website Revamp", date:"2026-07-20", expiry:"2026-08-05", amount:185000, status:"approved"},
      {id:"QUO-2026-0090", customer:"Bright Retail Solutions", project:"E-commerce Web App", date:"2026-07-18", expiry:"2026-08-02", amount:420000, status:"sent"},
      {id:"QUO-2026-0089", customer:"Nova Textiles", project:"Inventory Management System", date:"2026-07-15", expiry:"2026-07-30", amount:310000, status:"viewed"},
      {id:"QUO-2026-0088", customer:"Sunrise Hospitals", project:"Patient Portal", date:"2026-07-10", expiry:"2026-07-25", amount:265000, status:"draft"},
      {id:"QUO-2026-0087", customer:"Velocity Motors", project:"Dealer Management Website", date:"2026-07-05", expiry:"2026-08-05", amount:150000, status:"expired"},
      {id:"QUO-2026-0086", customer:"Orbit Learning Academy", project:"LMS Platform", date:"2026-06-28", expiry:"2026-07-13", amount:390000, status:"rejected"},
      {id:"QUO-2026-0085", customer:"Green Leaf Organics", project:"Organic Store Website", date:"2026-06-20", expiry:"2026-07-05", amount:95000, status:"approved"},
      {id:"QUO-2026-0084", customer:"Pinnacle Constructions", project:"Real Estate Showcase Site", date:"2026-06-15", expiry:"2026-06-30", amount:175000, status:"approved"}
    ],
    invoices: [
      {id:"INV-2026-0150", customer:"Zen Foods Pvt Ltd", date:"2026-07-25", due:"2026-08-08", amount:185000, paid:185000, status:"paid", gstType:"gst"},
      {id:"INV-2026-0149", customer:"Bright Retail Solutions", date:"2026-07-20", due:"2026-08-04", amount:420000, paid:200000, status:"partial", gstType:"gst"},
      {id:"INV-2026-0148", customer:"Nova Textiles", date:"2026-07-12", due:"2026-07-27", amount:310000, paid:0, status:"pending", gstType:"gst"},
      {id:"INV-2026-0147", customer:"Sunrise Hospitals", date:"2026-06-30", due:"2026-07-14", amount:265000, paid:0, status:"overdue", gstType:"gst"},
      {id:"INV-2026-0146", customer:"Green Leaf Organics", date:"2026-06-22", due:"2026-07-06", amount:95000, paid:95000, status:"paid", gstType:"gst"},
      {id:"INV-2026-0145", customer:"Pinnacle Constructions", date:"2026-06-18", due:"2026-07-02", amount:175000, paid:175000, status:"paid", gstType:"gst"},
      {id:"INV-2026-0144", customer:"Orbit Learning Academy", date:"2026-06-05", due:"2026-06-20", amount:390000, paid:0, status:"cancelled", gstType:"gst"},
      {id:"INV-2026-0143", customer:"Velocity Motors", date:"2026-05-28", due:"2026-06-12", amount:150000, paid:60000, status:"partial", gstType:"gst"},
      {id:"BOS-2026-0001", customer:"Green Leaf Organics", date:"2026-07-08", due:"2026-07-22", amount:10000, paid:0, status:"pending", gstType:"nongst"},
      {id:"BOS-2026-0002", customer:"Green Leaf Organics", date:"2026-07-14", due:"2026-07-28", amount:15000, paid:0, status:"pending", gstType:"nongst"},
      {id:"BOS-2026-0003", customer:"Green Leaf Organics", date:"2026-07-21", due:"2026-08-04", amount:20000, paid:0, status:"pending", gstType:"nongst"},
      {id:"BOS-2026-0004", customer:"Green Leaf Organics", date:"2026-07-29", due:"2026-08-12", amount:25000, paid:0, status:"pending", gstType:"nongst"}
    ],
    payments: [
      {id:"PAY-1042", invoice:"INV-2026-0150", customer:"Zen Foods Pvt Ltd", date:"2026-07-26", amount:185000, mode:"UPI", ref:"UPI2607261234", by:"Vicky"},
      {id:"PAY-1041", invoice:"INV-2026-0149", customer:"Bright Retail Solutions", date:"2026-07-21", amount:200000, mode:"Bank Transfer", ref:"NEFT20072025", by:"Vicky"},
      {id:"PAY-1040", invoice:"INV-2026-0146", customer:"Green Leaf Organics", date:"2026-06-23", amount:95000, mode:"Cheque", ref:"CHQ004521", by:"Divya N"},
      {id:"PAY-1039", invoice:"INV-2026-0145", customer:"Pinnacle Constructions", date:"2026-06-19", amount:175000, mode:"Card", ref:"CARD887712", by:"Vicky"},
      {id:"PAY-1038", invoice:"INV-2026-0143", customer:"Velocity Motors", date:"2026-05-29", amount:60000, mode:"Cash", ref:"CASH-0021", by:"Divya N"}
    ],
    followups: [
      {id:1, customer:"Nova Textiles", type:"call", subject:"Discuss inventory system proposal", next:"2026-08-05", priority:"high", status:"pending", owner:"Vicky"},
      {id:2, customer:"Sunrise Hospitals", type:"meeting", subject:"Patient portal walkthrough", next:"2026-08-06", priority:"high", status:"pending", owner:"Divya N"},
      {id:3, customer:"Bright Retail Solutions", type:"email", subject:"Send revised quotation", next:"2026-08-04", priority:"medium", status:"pending", owner:"Vicky"},
      {id:4, customer:"Orbit Learning Academy", type:"whatsapp", subject:"Follow up on LMS rejection reason", next:"2026-08-07", priority:"low", status:"pending", owner:"Karthik R"},
      {id:5, customer:"Velocity Motors", type:"call", subject:"Balance payment reminder", next:"2026-08-02", priority:"high", status:"completed", owner:"Vicky"}
    ],
    activities: [
      {icon:"bi-file-earmark-check", color:"success", text:"Quotation <b>QUO-2026-0091</b> approved by Zen Foods Pvt Ltd", time:"2 hours ago"},
      {icon:"bi-cash-coin", color:"primary", text:"Payment of <b>₹1,85,000</b> received for INV-2026-0150", time:"5 hours ago"},
      {icon:"bi-receipt", color:"info", text:"Invoice <b>INV-2026-0149</b> sent to Bright Retail Solutions", time:"Yesterday"},
      {icon:"bi-person-plus", color:"purple", text:"New customer <b>Pinnacle Constructions</b> added", time:"2 days ago"},
      {icon:"bi-exclamation-triangle", color:"warning", text:"Quotation <b>QUO-2026-0087</b> expired without response", time:"3 days ago"}
    ],

    /* Projects — richer records used by Projects, Documents, Payments/Ledger, Quotation/Invoice project pickers */
    projects: [
      {id:1, name:"Corporate Website Revamp", customer:"Zen Foods Pvt Ltd", type:"Website", status:"completed", amount:185000, services:["Website Designing","Web Development","SEO Services"],
        attachments:[{name:"Requirements.pdf", size:"220 KB", icon:"bi-file-earmark-pdf"},{name:"Logo_Assets.zip", size:"4.1 MB", icon:"bi-file-earmark-zip"}]},
      {id:2, name:"Logo & Branding Kit", customer:"Zen Foods Pvt Ltd", type:"Website", status:"completed", amount:35000, services:["Logo & Branding"],
        attachments:[{name:"Brand_Guidelines.pdf", size:"1.2 MB", icon:"bi-file-earmark-pdf"}]},
      {id:3, name:"E-commerce Web App", customer:"Bright Retail Solutions", type:"Web Application", status:"inprogress", amount:420000, services:["Web Development","Domain Registration","Web Hosting"],
        attachments:[{name:"Product_Catalog.xlsx", size:"88 KB", icon:"bi-file-earmark-excel"},{name:"Wireframes.pdf", size:"3.4 MB", icon:"bi-file-earmark-pdf"}]},
      {id:4, name:"Inventory Management System", customer:"Nova Textiles", type:"Web Application", status:"inprogress", amount:310000, services:["Web Development","AMC (Annual Maintenance)"],
        attachments:[{name:"Scope_Document.docx", size:"140 KB", icon:"bi-file-earmark-word"}]},
      {id:5, name:"Patient Portal", customer:"Sunrise Hospitals", type:"Web Application", status:"overdue", amount:265000, services:["Web Development","Web Hosting"],
        attachments:[{name:"HIPAA_Compliance.pdf", size:"640 KB", icon:"bi-file-earmark-pdf"}]},
      {id:6, name:"Dealer Management Website", customer:"Velocity Motors", type:"Website", status:"planning", amount:150000, services:["Website Designing","Domain Registration"],
        attachments:[]},
      {id:7, name:"LMS Platform", customer:"Orbit Learning Academy", type:"Web Application", status:"review", amount:390000, services:["Web Development","Video Editing"],
        attachments:[{name:"Course_Structure.pdf", size:"310 KB", icon:"bi-file-earmark-pdf"}]},
      {id:8, name:"Organic Store Website", customer:"Green Leaf Organics", type:"Website", status:"review", amount:15000, services:["Website Designing"],
        attachments:[{name:"Product_Photos.zip", size:"12 MB", icon:"bi-file-earmark-zip"}]},
      {id:9, name:"Domain & Hosting Renewal", customer:"Green Leaf Organics", type:"Website", status:"completed", amount:20000, services:["Domain Registration","Web Hosting"],
        attachments:[]},
      {id:10, name:"AMC Support Contract", customer:"Green Leaf Organics", type:"Website", status:"inprogress", amount:25000, services:["AMC (Annual Maintenance)"],
        attachments:[]},
      {id:11, name:"Real Estate Showcase Site", customer:"Pinnacle Constructions", type:"Website", status:"planning", amount:175000, services:["Website Designing","SEO Services"],
        attachments:[{name:"Property_List.xlsx", size:"64 KB", icon:"bi-file-earmark-excel"}]}
    ],

    /* Notifications — carry a dueDate so the combined Calendar can plot them */
    notifications: [
      {id:1, type:"Domain Renewal", icon:"bi-globe2", color:"warning", title:"Domain renewal due", desc:"brightretail.in expires on 09 Aug 2026 (5 days left). Renew to avoid downtime and email disruption.", time:"2 hours ago", dueDate:"2026-08-09", link:"customer-details.php", linkLabel:"View Customer"},
      {id:2, type:"Hosting Renewal", icon:"bi-hdd-network", color:"info", title:"Hosting renewal due", desc:"zenfoods.com hosting plan expires on 13 Aug 2026 (9 days left).", time:"5 hours ago", dueDate:"2026-08-13", link:"customer-details.php", linkLabel:"View Customer"},
      {id:3, type:"Pending Payment", icon:"bi-cash-coin", color:"danger", title:"Payment overdue", desc:"INV-2026-0147 balance ₹2,65,000 is overdue by 3 days for Sunrise Hospitals.", time:"Yesterday", dueDate:"2026-08-01", link:"invoice-list.php", linkLabel:"View Invoice"},
      {id:4, type:"Quotation Expiry", icon:"bi-file-earmark-excel", color:"purple", title:"Quotation expiring tomorrow", desc:"QUO-2026-0089 for Nova Textiles expires on 05 Aug 2026.", time:"Yesterday", dueDate:"2026-08-05", link:"quotation-list.php", linkLabel:"View Quotation"},
      {id:5, type:"Invoice Due", icon:"bi-receipt", color:"primary", title:"Invoice payment due", desc:"INV-2026-0150 due on 08 Aug 2026 for Zen Foods Pvt Ltd.", time:"2 days ago", dueDate:"2026-08-08", link:"invoice-list.php", linkLabel:"View Invoice"},
      {id:6, type:"Quotation", icon:"bi-person-check", color:"success", title:"Quotation approved", desc:"Zen Foods Pvt Ltd approved QUO-2026-0091 worth ₹1,85,000.", time:"3 days ago", dueDate:"2026-07-20", link:"quotation-list.php", linkLabel:"View Quotation"}
    ],

    /* Domain / Hosting renewal tracking — one row per live project */
    renewals: [
      {id:1, project:"Corporate Website Revamp", customer:"Zen Foods Pvt Ltd", type:"Website", server:"Hostinger", domainControl:"Client-side (GoDaddy)", liveLink:"https://zenfoods.com — admin: zenfoods.com/wp-admin", expiryDate:"2026-08-18", renewalType:"Yearly", paidStatus:"Paid"},
      {id:2, project:"E-commerce Web App", customer:"Bright Retail Solutions", type:"Web Application", server:"AWS", domainControl:"With us (KTG)", liveLink:"https://brightretail.in — admin: brightretail.in/admin", expiryDate:"2026-07-25", renewalType:"Yearly", paidStatus:"Unpaid"},
      {id:3, project:"Inventory Management System", customer:"Nova Textiles", type:"Web Application", server:"Own VPS / Server", domainControl:"With us (KTG)", liveLink:"http://inventory.novatextiles.com — admin login pending", expiryDate:"2026-09-10", renewalType:"Yearly", paidStatus:"Hold"},
      {id:4, project:"Patient Portal", customer:"Sunrise Hospitals", type:"Web Application", server:"Bluehost", domainControl:"Client-side", liveLink:"https://sunrisehosp.com/portal", expiryDate:"2026-08-30", renewalType:"Half-Yearly", paidStatus:"Paid"},
      {id:5, project:"Dealer Management Website", customer:"Velocity Motors", type:"Website", server:"GoDaddy", domainControl:"Client-side (GoDaddy)", liveLink:"https://velocitymotors.in", expiryDate:"2026-12-05", renewalType:"Yearly", paidStatus:"Paid"},
      {id:6, project:"LMS Platform", customer:"Orbit Learning Academy", type:"Web Application", server:"Hostinger", domainControl:"With us (KTG)", liveLink:"https://orbitlearning.edu — admin: orbitlearning.edu/cp", expiryDate:"2026-07-15", renewalType:"Monthly", paidStatus:"Overdue"},
      {id:7, project:"Organic Store Website", customer:"Green Leaf Organics", type:"Website", server:"GoDaddy", domainControl:"Client-side (GoDaddy)", liveLink:"https://greenleaforganics.com", expiryDate:"2026-08-05", renewalType:"Yearly", paidStatus:"Unpaid"},
      {id:8, project:"Real Estate Showcase Site", customer:"Pinnacle Constructions", type:"Website", server:"Bluehost", domainControl:"With us (KTG)", liveLink:"https://pinnacleco.in", expiryDate:"2027-01-20", renewalType:"Yearly", paidStatus:"Paid"}
    ]
  };

  /* ---------------- Query String Helper ---------------- */
  function qs(name) {
    return new URLSearchParams(location.search).get(name);
  }

  /* ---------------- Auth ---------------- */
  const Auth = {
    isLoggedIn() { return sessionStorage.getItem('ktg_auth') === '1'; },
    login(email, remember) {
      sessionStorage.setItem('ktg_auth', '1');
      sessionStorage.setItem('ktg_user_email', email || 'ktgkrr@gmail.com');
      if (remember) localStorage.setItem('ktg_remember_email', email || '');
      return true;
    },
    currentEmail() { return sessionStorage.getItem('ktg_user_email') || 'ktgkrr@gmail.com'; },
    logout() {
      sessionStorage.removeItem('ktg_auth');
      sessionStorage.removeItem('ktg_user_email');
      window.location.href = 'login.php';
    }
  };

  /* ---------------- Masters (Project Types / Services / Taxes / Follow Up Types / Servers / Renewal Types / Paid Status) ---------------- */
  const MASTERS_KEY = 'ktg_masters_v4';
  const MASTERS_SEED = {
    projectTypes: ["Website", "Web Application", "Mobile Application", "E-Commerce Platform", "ERP Software", "Digital Marketing"],
    services: [
      {name:"Website Designing", rate:35000},
      {name:"Web Development", rate:65000},
      {name:"Mobile App Development", rate:85000},
      {name:"Domain Registration", rate:1200},
      {name:"Web Hosting", rate:6000},
      {name:"AMC (Annual Maintenance)", rate:15000},
      {name:"Renewal Charges", rate:5000},
      {name:"Video Editing", rate:2500},
      {name:"Logo & Branding", rate:12000},
      {name:"SEO Services", rate:18000}
    ],
    taxes: [
      {name:"GST 18%", percent:18},
      {name:"GST 12%", percent:12},
      {name:"GST 5%", percent:5},
      {name:"No Tax", percent:0}
    ],
    followupTypes: ["Call", "Meeting", "Email", "WhatsApp"],
    servers: ["Hostinger", "GoDaddy", "AWS", "Bluehost", "Own VPS / Server"],
    renewalTypes: ["Monthly", "Quarterly", "Half-Yearly", "Yearly"],
    paidStatusTypes: ["Paid", "Unpaid", "Hold", "Overdue"]
  };
  const Masters = {
    get() {
      let m = JSON.parse(localStorage.getItem(MASTERS_KEY) || 'null');
      if (!m) { m = JSON.parse(JSON.stringify(MASTERS_SEED)); localStorage.setItem(MASTERS_KEY, JSON.stringify(m)); }
      let dirty = false;
      ['followupTypes','servers','renewalTypes','paidStatusTypes'].forEach(k => {
        if (!m[k]) { m[k] = MASTERS_SEED[k].slice(); dirty = true; }
      });
      if (dirty) this.save(m);
      return m;
    },
    save(m) { localStorage.setItem(MASTERS_KEY, JSON.stringify(m)); },
    projectTypes() { return this.get().projectTypes; },
    services() { return this.get().services; },
    taxes() { return this.get().taxes; },
    followupTypes() { return this.get().followupTypes; },
    servers() { return this.get().servers; },
    renewalTypes() { return this.get().renewalTypes; },
    paidStatusTypes() { return this.get().paidStatusTypes; },
    addProjectType(name) { const m = this.get(); m.projectTypes.push(name); this.save(m); },
    removeProjectType(i) { const m = this.get(); m.projectTypes.splice(i,1); this.save(m); },
    addService(svc) { const m = this.get(); m.services.push(svc); this.save(m); },
    removeService(i) { const m = this.get(); m.services.splice(i,1); this.save(m); },
    addTax(tax) { const m = this.get(); m.taxes.push(tax); this.save(m); },
    removeTax(i) { const m = this.get(); m.taxes.splice(i,1); this.save(m); },
    addFollowupType(name) { const m = this.get(); m.followupTypes.push(name); this.save(m); },
    removeFollowupType(i) { const m = this.get(); m.followupTypes.splice(i,1); this.save(m); },
    addServer(name) { const m = this.get(); m.servers.push(name); this.save(m); },
    removeServer(i) { const m = this.get(); m.servers.splice(i,1); this.save(m); },
    addRenewalType(name) { const m = this.get(); m.renewalTypes.push(name); this.save(m); },
    removeRenewalType(i) { const m = this.get(); m.renewalTypes.splice(i,1); this.save(m); },
    addPaidStatusType(name) { const m = this.get(); m.paidStatusTypes.push(name); this.save(m); },
    removePaidStatusType(i) { const m = this.get(); m.paidStatusTypes.splice(i,1); this.save(m); },
    reset() { localStorage.removeItem(MASTERS_KEY); }
  };

  /* ---------------- Company Profile (separate letterhead per GST / Non-GST) ----------------
     A field left blank on the Settings page simply doesn't render on the letterhead — see
     companyFieldsHtml() below — rather than showing an empty "Label:" line. */
  const COMPANY_KEY = 'ktg_company_profile_v1';
  const COMPANY_SEED = {
    gst: {
      name: "Knock The Globe Technologies", address: "4th Cross Rd, Vaiyapuri Nagar, Karur, Tamil Nadu 639002",
      phone: "72001 23452", email: "knockus@knocktheglobe.com", website: "www.knocktheglobe.com",
      gstin: "33ABCDE1234F1Z5", pan: "ABCDE1234F"
    },
    nongst: {
      name: "Knock The Globe Technologies", address: "4th Cross Rd, Vaiyapuri Nagar, Karur, Tamil Nadu 639002",
      phone: "72001 23452", email: "knockus@knocktheglobe.com", website: "www.knocktheglobe.com",
      gstin: "", pan: ""
    }
  };
  const CompanyProfile = {
    _load() {
      let c = JSON.parse(localStorage.getItem(COMPANY_KEY) || 'null');
      if (!c) { c = JSON.parse(JSON.stringify(COMPANY_SEED)); localStorage.setItem(COMPANY_KEY, JSON.stringify(c)); }
      if (!c.gst) c.gst = JSON.parse(JSON.stringify(COMPANY_SEED.gst));
      if (!c.nongst) c.nongst = JSON.parse(JSON.stringify(COMPANY_SEED.nongst));
      return c;
    },
    get(gstType) { return this._load()[gstType === 'nongst' ? 'nongst' : 'gst']; },
    save(gstType, data) {
      const c = this._load();
      c[gstType === 'nongst' ? 'nongst' : 'gst'] = data;
      localStorage.setItem(COMPANY_KEY, JSON.stringify(c));
    }
  };

  /* ---------------- Notifications (read / cleared state persisted) ---------------- */
  const NOTIF_KEY = 'ktg_notif_state_v1';
  function notifState() {
    return JSON.parse(localStorage.getItem(NOTIF_KEY) || '{"read":[],"cleared":[]}');
  }
  function saveNotifState(s) { localStorage.setItem(NOTIF_KEY, JSON.stringify(s)); }
  const Notifications = {
    list() {
      const s = notifState();
      return DATA.notifications
        .filter(n => !s.cleared.includes(n.id))
        .map(n => ({...n, read: s.read.includes(n.id)}))
        .sort((a,b) => a.read === b.read ? 0 : (a.read ? 1 : -1));
    },
    get(id) {
      const s = notifState();
      const n = DATA.notifications.find(n => n.id === Number(id));
      return n ? {...n, read: s.read.includes(n.id)} : null;
    },
    unreadCount() { return this.list().filter(n => !n.read).length; },
    markRead(id) { const s = notifState(); if (!s.read.includes(Number(id))) s.read.push(Number(id)); saveNotifState(s); },
    markAllRead() { const s = notifState(); DATA.notifications.forEach(n => { if (!s.read.includes(n.id)) s.read.push(n.id); }); saveNotifState(s); },
    clear(id) { const s = notifState(); if (!s.cleared.includes(Number(id))) s.cleared.push(Number(id)); saveNotifState(s); },
    clearAll() { const s = notifState(); s.cleared = DATA.notifications.map(n => n.id); saveNotifState(s); }
  };

  /* ---------------- Ledger (customer-wise bulk payments, allocated FIFO across their invoices) ---------------- */
  const LEDGER_KEY = 'ktg_ledger_v3';
  // Seeded to exactly reconstruct each invoice's baked-in `paid` amount, so Payment History
  // always explains the totals shown in the customer summary (single source of truth).
  const LEDGER_SEED = {
    payments: [
      {id:"PAY-1042", customer:"Zen Foods Pvt Ltd", date:"2026-07-26", amount:185000, mode:"UPI", ref:"UPI2607261234", by:"Vicky", remarks:"", allocations:[{invoice:"INV-2026-0150", amount:185000}]},
      {id:"PAY-1041", customer:"Bright Retail Solutions", date:"2026-07-21", amount:200000, mode:"Bank Transfer", ref:"NEFT20072025", by:"Vicky", remarks:"", allocations:[{invoice:"INV-2026-0149", amount:200000}]},
      {id:"PAY-1040", customer:"Green Leaf Organics", date:"2026-06-23", amount:95000, mode:"Cheque", ref:"CHQ004521", by:"Divya N", remarks:"", allocations:[{invoice:"INV-2026-0146", amount:95000}]},
      {id:"PAY-1039", customer:"Pinnacle Constructions", date:"2026-06-19", amount:175000, mode:"Card", ref:"CARD887712", by:"Vicky", remarks:"", allocations:[{invoice:"INV-2026-0145", amount:175000}]},
      {id:"PAY-1038", customer:"Velocity Motors", date:"2026-05-29", amount:60000, mode:"Cash", ref:"CASH-0021", by:"Divya N", remarks:"", allocations:[{invoice:"INV-2026-0143", amount:60000}]}
    ]
  };
  function ledgerData() {
    let l = JSON.parse(localStorage.getItem(LEDGER_KEY) || 'null');
    if (!l) { l = JSON.parse(JSON.stringify(LEDGER_SEED)); localStorage.setItem(LEDGER_KEY, JSON.stringify(l)); }
    return l;
  }
  function saveLedger(l) { localStorage.setItem(LEDGER_KEY, JSON.stringify(l)); }
  const Ledger = {
    // Invoices for a customer, oldest first, with paid/balance derived entirely from ledger allocations
    invoicesForCustomer(customer) {
      const l = ledgerData();
      return DATA.invoices
        .filter(i => i.customer === customer && i.status !== 'cancelled')
        .map(i => {
          const paid = l.payments.reduce((sum, pay) => {
            if (pay.customer !== customer) return sum;
            const alloc = pay.allocations.find(a => a.invoice === i.id);
            return sum + (alloc ? alloc.amount : 0);
          }, 0);
          return {...i, paid, balance: Math.max(i.amount - paid, 0)};
        })
        .sort((a,b) => a.date.localeCompare(b.date));
    },
    paymentsForCustomer(customer) {
      return ledgerData().payments.filter(p => p.customer === customer).sort((a,b) => b.date.localeCompare(a.date));
    },
    allPayments() { return ledgerData().payments.slice().sort((a,b) => b.date.localeCompare(a.date)); },
    customerSummary(customer) {
      const invoices = this.invoicesForCustomer(customer);
      const total = invoices.reduce((s,i) => s + i.amount, 0);
      const paid = invoices.reduce((s,i) => s + i.paid, 0);
      return { total, paid, balance: total - paid, invoices };
    },
    allCustomerSummaries() {
      const names = [...new Set(DATA.invoices.map(i => i.customer))];
      return names.map(name => ({ customer: name, ...this.customerSummary(name) }));
    },
    // Given a lump sum, apply it oldest-invoice-first and return the breakdown (no side effects)
    suggestAllocation(customer, amount) {
      let remaining = Math.max(+amount || 0, 0);
      const breakdown = [];
      this.invoicesForCustomer(customer).forEach(inv => {
        if (inv.balance <= 0) { breakdown.push({invoice: inv, applied: 0, resultBalance: 0, status: 'already-paid'}); return; }
        const applied = Math.min(remaining, inv.balance);
        remaining -= applied;
        const resultBalance = inv.balance - applied;
        breakdown.push({
          invoice: inv, applied, resultBalance,
          status: applied === 0 ? 'untouched' : resultBalance === 0 ? 'covered' : 'partial'
        });
      });
      return { breakdown, unallocated: remaining };
    },
    recordPayment({customer, date, amount, mode, ref, by, remarks}) {
      const { breakdown } = this.suggestAllocation(customer, amount);
      const allocations = breakdown.filter(b => b.applied > 0).map(b => ({invoice: b.invoice.id, amount: b.applied}));
      const l = ledgerData();
      const id = 'PAY-' + (2000 + l.payments.length + 1);
      const record = {id, customer, date, amount: +amount, mode, ref, by, remarks, allocations};
      l.payments.push(record);
      saveLedger(l);
      return record;
    },
    reset() { localStorage.removeItem(LEDGER_KEY); }
  };

  /* ---------------- Layout Init ---------------- */
  // Sidebar/topbar/footer are now rendered server-side via PHP includes (components/*.php),
  // so this just wires up the interactive behavior once the DOM is ready.
  function loadLayout() {
    initSidebarNav();
    initSidebarToggle();
    initSidebarSubmenus();
    initTooltips();
    initLogout();
    initNotifications();
    initUserMeta();
    initSearchableSelects();
  }

  /* Turns every <select> on the page into a simple searchable dropdown, including ones
     populated later via JS (customer/service/filter lists etc.). Built from scratch instead
     of a third-party widget: the option panel is appended to <body> with position:fixed,
     positioned from the trigger's getBoundingClientRect() — so it can never be clipped by
     an ancestor's overflow:auto/hidden (tables, modals, cards), which is what broke the
     previous Choices.js-based version. The real <select> is kept (hidden) as the single
     source of truth: picking an option sets its value and dispatches a real 'change' event,
     so every existing onchange="..."/addEventListener('change',...) keeps working untouched.
     Add class="no-search" to a <select> to leave it as a plain native dropdown. */
  function initSearchableSelects() {
    let openState = null; // { select, trigger, panel, onDocMouseDown, onKeydown, onScrollResize }

    function closeOpen() {
      if (!openState) return;
      const { trigger, panel, onDocMouseDown, onKeydown, onScrollResize } = openState;
      trigger.classList.remove('open');
      panel.remove();
      document.removeEventListener('mousedown', onDocMouseDown, true);
      document.removeEventListener('keydown', onKeydown, true);
      window.removeEventListener('scroll', onScrollResize, true);
      window.removeEventListener('resize', onScrollResize);
      openState = null;
    }

    /* Look up the label by matching value rather than trusting selectedIndex — a select whose
       only "selected" option is also disabled (our placeholder pattern) isn't reliably
       reflected in selectedIndex/value resolution the same way across all cases, so match
       explicitly instead. A page marks its placeholder option with data-placeholder="1"
       (falls back to an empty-value disabled option if that attribute isn't present) — this
       is checked independently of select.value so a mis-resolved selectedIndex can't hide it. */
    function refreshTrigger(select, trigger) {
      const label = trigger.querySelector('.ktg-ss-label');
      const options = Array.from(select.options);
      const placeholderOpt = options.find(o => o.hasAttribute('data-placeholder')) || options.find(o => o.value === '' && o.disabled);
      const current = options.find(o => o !== placeholderOpt && o.value === select.value);
      const isPlaceholder = !current;
      const text = isPlaceholder ? (placeholderOpt ? placeholderOpt.textContent : '') : current.textContent;
      label.textContent = text || 'Select...';
      label.classList.toggle('placeholder', isPlaceholder);
    }

    function openPanel(select, trigger) {
      closeOpen();
      const panel = document.createElement('div');
      panel.className = 'ktg-ss-panel';

      const search = document.createElement('input');
      search.type = 'text';
      search.className = 'ktg-ss-search';
      search.placeholder = 'Search...';

      const optionsWrap = document.createElement('div');
      optionsWrap.className = 'ktg-ss-options';

      panel.appendChild(search);
      panel.appendChild(optionsWrap);
      document.body.appendChild(panel);

      const choices = Array.from(select.options)
        .filter(o => !(o.disabled && !o.value))
        .map(o => ({ value: o.value, label: o.textContent }));

      const renderOptions = (filterText) => {
        const q = filterText.trim().toLowerCase();
        const matches = q ? choices.filter(c => c.label.toLowerCase().includes(q)) : choices;
        optionsWrap.innerHTML = '';
        if (!matches.length) {
          const empty = document.createElement('div');
          empty.className = 'ktg-ss-empty';
          empty.textContent = 'No matches';
          optionsWrap.appendChild(empty);
          return;
        }
        matches.forEach(c => {
          const row = document.createElement('div');
          row.className = 'ktg-ss-option' + (c.value === select.value ? ' active' : '');
          row.textContent = c.label;
          row.addEventListener('click', () => {
            select.value = c.value;
            closeOpen();
            refreshTrigger(select, trigger);
            select.dispatchEvent(new Event('change', { bubbles: true }));
          });
          optionsWrap.appendChild(row);
        });
      };
      renderOptions('');
      search.addEventListener('input', () => renderOptions(search.value));
      search.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          const first = optionsWrap.querySelector('.ktg-ss-option');
          if (first) first.click();
        }
      });

      const position = () => {
        const rect = trigger.getBoundingClientRect();
        const panelHeight = panel.offsetHeight || 260;
        const spaceBelow = window.innerHeight - rect.bottom;
        const openAbove = spaceBelow < panelHeight + 8 && rect.top > spaceBelow;
        panel.style.left = Math.max(8, rect.left) + 'px';
        panel.style.width = Math.max(rect.width, 200) + 'px';
        panel.style.top = openAbove ? '' : (rect.bottom + 4) + 'px';
        panel.style.bottom = openAbove ? (window.innerHeight - rect.top + 4) + 'px' : '';
      };
      position();

      const onDocMouseDown = (e) => { if (!panel.contains(e.target) && e.target !== trigger) closeOpen(); };
      const onKeydown = (e) => { if (e.key === 'Escape') closeOpen(); };
      const onScrollResize = () => closeOpen();
      document.addEventListener('mousedown', onDocMouseDown, true);
      document.addEventListener('keydown', onKeydown, true);
      window.addEventListener('scroll', onScrollResize, true);
      window.addEventListener('resize', onScrollResize);

      trigger.classList.add('open');
      search.focus();
      openState = { select, trigger, panel, onDocMouseDown, onKeydown, onScrollResize };
    }

    function enhance(select) {
      if (!select || select.tagName !== 'SELECT' || select.multiple || select.classList.contains('no-search') || select.dataset.ssInit) return;
      select.dataset.ssInit = '1';

      const wrap = document.createElement('div');
      wrap.className = 'ktg-ss';
      const styleAttr = select.getAttribute('style');
      if (styleAttr) { wrap.setAttribute('style', styleAttr); select.removeAttribute('style'); }

      select.parentNode.insertBefore(wrap, select);
      select.style.display = 'none';
      wrap.appendChild(select);

      const trigger = document.createElement('button');
      trigger.type = 'button';
      trigger.className = (select.className ? select.className + ' ' : '') + 'ktg-ss-trigger';
      if (select.disabled) trigger.disabled = true;
      trigger.innerHTML = '<span class="ktg-ss-label"></span><i class="bi bi-chevron-down ktg-ss-caret"></i>';
      wrap.appendChild(trigger);
      refreshTrigger(select, trigger);

      trigger.addEventListener('click', () => {
        if (trigger.disabled) return;
        if (openState && openState.select === select) { closeOpen(); return; }
        openPanel(select, trigger);
      });

      select.dataset.ssTrigger = '1';
      select._ktgTrigger = trigger;
    }

    function scan() {
      document.querySelectorAll('select').forEach(select => {
        if (select.dataset.ssInit) {
          if (select._ktgTrigger) refreshTrigger(select, select._ktgTrigger);
          return;
        }
        enhance(select);
      });
    }
    scan();

    let pending = false;
    const observer = new MutationObserver(() => {
      if (pending) return;
      pending = true;
      requestAnimationFrame(() => { pending = false; scan(); });
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // Safety net: code that sets select.value directly (e.g. a pre-selected customer from a
    // URL param) doesn't fire any DOM mutation, so the trigger label wouldn't otherwise resync.
    setInterval(scan, 800);
  }

  function initSidebarNav() {
    const page = document.body.getAttribute('data-page');
    if (!page) return;
    document.querySelectorAll('.side-link').forEach(link => {
      if (link.getAttribute('data-nav') === page) link.classList.add('active');
    });
  }

  function initSidebarSubmenus() {
    const page = document.body.getAttribute('data-page');
    document.querySelectorAll('.has-submenu').forEach(row => {
      const caretBtn = row.querySelector('.submenu-caret-btn');
      const submenu = row.nextElementSibling;
      if (!caretBtn || !submenu || !submenu.classList.contains('side-submenu')) return;
      if (row.getAttribute('data-nav') === page) {
        caretBtn.classList.add('open');
        submenu.classList.add('open');
      }
      caretBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        caretBtn.classList.toggle('open');
        submenu.classList.toggle('open');
      });
    });
  }

  function initSidebarToggle() {
    const sidebar = document.getElementById('appSidebar');
    const mainWrap = document.getElementById('mainWrap');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const mobileToggle = document.getElementById('mobileSidebarToggle');
    const backdrop = document.getElementById('mobileBackdrop');

    if (collapseBtn) {
      const saved = localStorage.getItem('ktg-sidebar-collapsed') === '1';
      if (saved) { sidebar.classList.add('collapsed'); mainWrap.classList.add('expanded'); }
      collapseBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainWrap.classList.toggle('expanded');
        localStorage.setItem('ktg-sidebar-collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
      });
    }
    if (mobileToggle) {
      mobileToggle.addEventListener('click', () => {
        sidebar.classList.add('mobile-open');
        backdrop.classList.add('show');
      });
    }
    if (backdrop) {
      backdrop.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        backdrop.classList.remove('show');
      });
    }
  }

  function initLogout() {
    document.querySelectorAll('.js-logout').forEach(el => {
      el.addEventListener('click', (e) => {
        e.preventDefault();
        confirmDialog({
          title: 'Log out?',
          body: 'You will need to sign in again to access the dashboard.',
          confirmText: 'Log Out', confirmClass: 'btn-danger',
          onConfirm: () => Auth.logout()
        });
      });
    });
  }

  function initUserMeta() {
    document.querySelectorAll('.js-user-email').forEach(el => el.textContent = Auth.currentEmail());
  }

  function initNotifications() {
    const bellBtn = document.getElementById('notifBellBtn');
    if (!bellBtn) return;
    renderNotifDropdown();
    const markAllBtn = document.getElementById('notifMarkAllRead');
    if (markAllBtn) markAllBtn.addEventListener('click', (e) => { e.preventDefault(); Notifications.markAllRead(); renderNotifDropdown(); });
  }

  function renderNotifDropdown() {
    const badge = document.getElementById('notifBadge');
    const list = document.getElementById('notifDropdownList');
    if (!list) return;
    const items = Notifications.list();
    const unread = items.filter(n => !n.read).length;
    if (badge) { badge.textContent = unread; badge.style.display = unread ? 'flex' : 'none'; }
    list.innerHTML = items.slice(0, 6).map(n => `
      <a href="notifications.php?id=${n.id}" class="dropdown-item align-items-start py-2 ${n.read ? '' : 'bg-soft-primary bg-opacity-10'}">
        <i class="bi ${n.icon} text-${n.color} mt-1"></i>
        <div class="flex-grow-1">
          <div class="fw-600 d-flex align-items-center gap-2">${n.title} ${!n.read ? '<span class="dot-unread"></span>' : ''}</div>
          <div class="fs-11 text-muted">${n.desc}</div>
        </div>
      </a>`).join('') || `<div class="text-center text-muted fs-12 py-4">You're all caught up!</div>`;
  }

  function initTooltips() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
  }

  /* ---------------- Toasts ---------------- */
  function toast(message, type = 'primary', title = 'Notification') {
    let container = document.getElementById('toastStack');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastStack';
      container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
      document.body.appendChild(container);
    }
    const icons = { primary:'bi-info-circle-fill', success:'bi-check-circle-fill', danger:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill' };
    const el = document.createElement('div');
    el.className = 'toast align-items-center border-0';
    el.setAttribute('role', 'alert');
    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body d-flex align-items-start gap-2">
          <i class="bi ${icons[type] || icons.primary} text-${type} fs-5"></i>
          <div><strong class="d-block">${title}</strong><span class="fs-13 text-secondary">${message}</span></div>
        </div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>`;
    container.appendChild(el);
    const t = new bootstrap.Toast(el, { delay: 3500 });
    t.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  }

  /* ---------------- Skeleton loading simulation ---------------- */
  function showSkeleton(targetSelector, duration = 700) {
    const target = document.querySelector(targetSelector);
    if (!target) return;
    const real = target.querySelectorAll('.real-content');
    const skel = target.querySelectorAll('.skeleton-content');
    skel.forEach(s => s.classList.remove('d-none'));
    real.forEach(r => r.classList.add('d-none'));
    setTimeout(() => {
      skel.forEach(s => s.classList.add('d-none'));
      real.forEach(r => { r.classList.remove('d-none'); r.classList.add('fade-in-up'); });
    }, duration);
  }

  /* ---------------- Formatting helpers ---------------- */
  function money(n) {
    return '₹' + Number(n).toLocaleString('en-IN');
  }
  function moneyShort(n) {
    if (n >= 10000000) return '₹' + (n/10000000).toFixed(2) + 'Cr';
    if (n >= 100000) return '₹' + (n/100000).toFixed(2) + 'L';
    if (n >= 1000) return '₹' + (n/1000).toFixed(1) + 'K';
    return '₹' + n;
  }

  /* ---------------- CSV export (stands in for "Excel" export, no external libs) ---------------- */
  function exportCsv(filename, rows) {
    const csv = rows.map(r => r.map(cell => {
      const s = String(cell ?? '');
      return /[",\n]/.test(s) ? '"' + s.replace(/"/g,'""') + '"' : s;
    }).join(',')).join('\r\n');
    const blob = new Blob(["﻿" + csv], {type: 'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = filename;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }

  /* ---------------- Custom Chart Engine (pure SVG/CSS, no external lib) ---------------- */
  const Charts = {
    // Vertical bar chart: data = [{label, value, color}]
    bar(el, data, opts = {}) {
      const max = opts.max || Math.max(...data.map(d => d.value)) * 1.15;
      el.innerHTML = '';
      const wrap = document.createElement('div');
      wrap.className = 'bar-chart';
      data.forEach((d, i) => {
        const col = document.createElement('div');
        col.className = 'bar-col';
        const heightPct = max ? (d.value / max) * 100 : 0;
        col.innerHTML = `
          <span class="fw-700 fs-12" style="color:${d.color || 'var(--primary)'}">${opts.valueFormatter ? opts.valueFormatter(d.value) : d.value}</span>
          <div class="bar-stack" style="height:${Math.max(heightPct, 3)}%;" data-bs-toggle="tooltip" title="${d.label}: ${opts.valueFormatter ? opts.valueFormatter(d.value) : d.value}">
            <div class="bar-seg" style="height:100%; background: ${d.color || 'var(--primary)'};"></div>
          </div>
          <div class="bar-label">${d.label}</div>`;
        wrap.appendChild(col);
      });
      el.appendChild(wrap);
      requestAnimationFrame(() => initTooltips());
    },

    // Grouped bar (two series) e.g. collected vs target
    groupedBar(el, data, s1 = 'collected', s2 = 'target') {
      const max = Math.max(...data.map(d => Math.max(d[s1], d[s2]))) * 1.15;
      el.innerHTML = '';
      const wrap = document.createElement('div');
      wrap.className = 'bar-chart';
      data.forEach(d => {
        const col = document.createElement('div');
        col.className = 'bar-col';
        col.innerHTML = `
          <div class="d-flex align-items-end gap-1" style="height:170px;">
            <div style="width:14px; height:${(d[s1]/max)*100}%; background: var(--primary); border-radius:6px 6px 2px 2px;" data-bs-toggle="tooltip" title="Collected: ${money(d[s1])}"></div>
            <div style="width:14px; height:${(d[s2]/max)*100}%; background: var(--border); border-radius:6px 6px 2px 2px;" data-bs-toggle="tooltip" title="Target: ${money(d[s2])}"></div>
          </div>
          <div class="bar-label">${d.m}</div>`;
        wrap.appendChild(col);
      });
      el.appendChild(wrap);
      requestAnimationFrame(() => initTooltips());
    },

    // Donut chart: data = [{label, value, color}]
    donut(el, data, opts = {}) {
      const size = opts.size || 160;
      const stroke = opts.stroke || 20;
      const r = (size - stroke) / 2;
      const c = 2 * Math.PI * r;
      const total = data.reduce((s, d) => s + d.value, 0);
      let offset = 0;
      let circles = '';
      data.forEach(d => {
        const frac = total ? d.value / total : 0;
        const len = frac * c;
        circles += `<circle cx="${size/2}" cy="${size/2}" r="${r}" fill="none" stroke="${d.color}" stroke-width="${stroke}"
          stroke-dasharray="${len} ${c - len}" stroke-dashoffset="${-offset}" stroke-linecap="butt"
          transform="rotate(-90 ${size/2} ${size/2})" data-bs-toggle="tooltip" title="${d.label}: ${d.value}"
          style="transition: stroke-dasharray .8s var(--ease); cursor:pointer;"/>`;
        offset += len;
      });
      el.innerHTML = `
        <div class="donut-wrap" style="width:${size}px;height:${size}px;">
          <svg width="${size}" height="${size}">${circles}</svg>
          <div class="donut-center"><span class="d-val">${total}</span><span class="d-label">${opts.centerLabel || 'Total'}</span></div>
        </div>`;
      requestAnimationFrame(() => initTooltips());
    },

    // Simple line/area chart: data = [{label, value}]
    line(el, data, opts = {}) {
      const w = opts.width || 560, h = opts.height || 200, pad = 28;
      const max = Math.max(...data.map(d => d.value)) * 1.15 || 1;
      const min = 0;
      const stepX = (w - pad * 2) / (data.length - 1);
      const points = data.map((d, i) => {
        const x = pad + i * stepX;
        const y = h - pad - ((d.value - min) / (max - min || 1)) * (h - pad * 1.5);
        return { x, y, ...d };
      });
      const pathD = points.map((p, i) => (i === 0 ? 'M' : 'L') + p.x + ',' + p.y).join(' ');
      const areaD = pathD + ` L${points[points.length-1].x},${h-pad} L${points[0].x},${h-pad} Z`;
      const dots = points.map(p => `<circle class="line-chart-point" cx="${p.x}" cy="${p.y}" r="4" fill="#fff" stroke="${opts.color || 'var(--primary)'}" stroke-width="2.5" data-bs-toggle="tooltip" title="${p.label}: ${opts.valueFormatter ? opts.valueFormatter(p.value) : p.value}"/>`).join('');
      const labels = points.map(p => `<text x="${p.x}" y="${h}" font-size="10.5" fill="#8994a8" text-anchor="middle">${p.label}</text>`).join('');
      el.innerHTML = `
        <div class="line-chart-wrap">
          <svg viewBox="0 0 ${w} ${h+14}">
            <defs>
              <linearGradient id="lineGrad${el.id}" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="${opts.color || '#2563eb'}" stop-opacity="0.25"/>
                <stop offset="100%" stop-color="${opts.color || '#2563eb'}" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <path d="${areaD}" fill="url(#lineGrad${el.id})" stroke="none"/>
            <path d="${pathD}" fill="none" stroke="${opts.color || 'var(--primary)'}" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
            ${dots}
            ${labels}
          </svg>
        </div>`;
      requestAnimationFrame(() => initTooltips());
    },

    // Tiny sparkline for stat cards
    sparkline(el, values, color = '#2563eb') {
      const w = 90, h = 34;
      const max = Math.max(...values), min = Math.min(...values);
      const stepX = w / (values.length - 1);
      const pts = values.map((v, i) => `${i*stepX},${h - ((v-min)/(max-min||1))*h}`).join(' ');
      el.innerHTML = `<svg width="${w}" height="${h}"><polyline points="${pts}" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
    }
  };

  /* ---------------- Confirmation Dialog ---------------- */
  function confirmDialog({ title = 'Are you sure?', body = 'This action cannot be undone.', confirmText = 'Confirm', confirmClass = 'btn-danger', onConfirm }) {
    let modalEl = document.getElementById('ktgConfirmModal');
    if (!modalEl) {
      modalEl = document.createElement('div');
      modalEl.className = 'modal fade';
      modalEl.id = 'ktgConfirmModal';
      modalEl.innerHTML = `
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
          <div class="modal-content">
            <div class="modal-body text-center pt-4">
              <div class="empty-state py-0">
                <div class="es-icon bg-soft-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <h6 id="ktgConfirmTitle"></h6>
                <p id="ktgConfirmBody"></p>
              </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
              <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button class="btn" id="ktgConfirmBtn">Confirm</button>
            </div>
          </div>
        </div>`;
      document.body.appendChild(modalEl);
    }
    modalEl.querySelector('#ktgConfirmTitle').textContent = title;
    modalEl.querySelector('#ktgConfirmBody').textContent = body;
    const btn = modalEl.querySelector('#ktgConfirmBtn');
    btn.className = 'btn ' + confirmClass;
    btn.textContent = confirmText;
    const modal = new bootstrap.Modal(modalEl);
    const handler = () => { modal.hide(); onConfirm && onConfirm(); btn.removeEventListener('click', handler); };
    btn.addEventListener('click', handler);
    modal.show();
  }

  /* ---------------- Responsive DataTables (mobile expand/collapse rows) ----------------
     Call after (re)populating a table's <tbody>. On narrow screens, shows only an "anchor"
     column (S.No/# if present, otherwise the first data column) plus `primaryCount` more
     data fields. The expand chevron is embedded inside the anchor cell itself (no extra
     column). Everything else — remaining fields and the trailing Actions column — moves
     into a full-width detail panel beneath the row, toggled by that chevron. Checkbox
     columns are hidden outright on mobile (not surfaced in the panel). This eliminates
     horizontal scrolling entirely rather than just reducing it. */
  function makeResponsiveTable(table, opts = {}) {
    if (!table) return;
    const primaryCount = opts.primaryCount || 2;
    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');
    if (!thead || !tbody) return;

    // Clean up anything injected by a previous call (safe to call repeatedly after re-render)
    table.querySelectorAll('tr.mobile-detail-row').forEach(r => r.remove());
    table.querySelectorAll('.mobile-toggle-btn').forEach(b => b.remove());
    table.querySelectorAll('.mobile-hide-cell').forEach(c => c.classList.remove('mobile-hide-cell'));
    table.querySelectorAll('.mobile-primary-cell').forEach(c => c.classList.remove('mobile-primary-cell'));

    const headThs = Array.from(thead.querySelectorAll('th'));
    const colCount = headThs.length;
    if (colCount < 2) return;

    const dataRows = Array.from(tbody.querySelectorAll(':scope > tr')).filter(r => r.children.length === colCount);
    if (!dataRows.length) return;

    const headerLabelOf = (i) => (headThs[i] ? headThs[i].textContent.trim().toLowerCase() : '');
    const isCheckboxCol = (i) => headThs[i] && headThs[i].querySelector('input[type="checkbox"]') !== null;
    const isIndexCol = (i) => ['s.no', '#', 'sno'].includes(headerLabelOf(i));
    const lastIdx = colCount - 1;

    // Anchor: the column that stays visible and carries the expand chevron
    let anchorIdx = -1;
    for (let i = 0; i < colCount; i++) { if (isIndexCol(i)) { anchorIdx = i; break; } }
    if (anchorIdx === -1) {
      for (let i = 0; i < colCount; i++) { if (!isCheckboxCol(i) && i !== lastIdx) { anchorIdx = i; break; } }
    }
    if (anchorIdx === -1) return;

    const checkboxIndices = [];
    for (let i = 0; i < colCount; i++) { if (isCheckboxCol(i)) checkboxIndices.push(i); }

    const remainingIndices = [];
    for (let i = 0; i < colCount; i++) {
      if (i === anchorIdx || i === lastIdx || checkboxIndices.includes(i)) continue;
      remainingIndices.push(i);
    }
    const visiblePrimary = remainingIndices.slice(0, primaryCount);
    const hiddenIndices = remainingIndices.slice(primaryCount);

    // Nothing to gain by transforming this table on mobile
    if (!hiddenIndices.length && !checkboxIndices.length && lastIdx === anchorIdx) return;

    const headerLabels = headThs.map(th => th.textContent.trim());

    // Header: hide checkbox / overflow / actions header cells (visiblePrimary + anchor stay)
    checkboxIndices.forEach(i => headThs[i].classList.add('mobile-hide-cell'));
    hiddenIndices.forEach(i => headThs[i].classList.add('mobile-hide-cell'));
    if (lastIdx !== anchorIdx) headThs[lastIdx].classList.add('mobile-hide-cell');

    dataRows.forEach(row => {
      const cells = Array.from(row.children);

      checkboxIndices.forEach(i => cells[i] && cells[i].classList.add('mobile-hide-cell'));
      hiddenIndices.forEach(i => cells[i] && cells[i].classList.add('mobile-hide-cell'));

      // Clone Actions content into the detail panel; the original cell stays untouched
      // (only CSS-hidden on mobile) so desktop keeps working exactly as before.
      let actionsHtml = '';
      if (lastIdx !== anchorIdx && cells[lastIdx]) {
        actionsHtml = cells[lastIdx].innerHTML;
        cells[lastIdx].classList.add('mobile-hide-cell');
      }

      const detailTd = document.createElement('td');
      detailTd.colSpan = colCount;
      const fieldsHtml = hiddenIndices.map(i => `
        <div class="mobile-detail-item">
          <span class="mdi-label">${headerLabels[i]}</span>
          <span class="mdi-value">${cells[i] ? cells[i].innerHTML : ''}</span>
        </div>`).join('');
      detailTd.innerHTML = fieldsHtml + (actionsHtml.trim() ? `<div class="mobile-detail-actions">${actionsHtml}</div>` : '');
      const detailRow = document.createElement('tr');
      detailRow.className = 'mobile-detail-row d-none';
      detailRow.appendChild(detailTd);
      row.after(detailRow);

      // Embed the chevron inside the anchor cell — no extra column needed
      const anchorCell = cells[anchorIdx];
      anchorCell.classList.add('mobile-primary-cell');
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'mobile-toggle-btn';
      btn.setAttribute('aria-label', 'Show more');
      btn.innerHTML = '<i class="bi bi-chevron-down"></i>';
      anchorCell.insertBefore(btn, anchorCell.firstChild);

      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = detailRow.classList.toggle('d-none');
        btn.querySelector('i').className = isHidden ? 'bi bi-chevron-down' : 'bi bi-chevron-up';
      });
    });
  }

  /* GST invoices use the INV- prefix, Non-GST (Bill of Supply) invoices use BOS- —
     each series numbers independently so switching tax mode doesn't skip numbers. */
  function nextInvoiceNumber(gstType){
    const prefix = gstType === 'nongst' ? 'BOS' : 'INV';
    const year = new Date().getFullYear();
    let max = 0;
    DATA.invoices.forEach(inv => {
      const m = inv.id.match(new RegExp(`^${prefix}-(\\d{4})-(\\d+)$`));
      if (m) max = Math.max(max, +m[2]);
    });
    return `${prefix}-${year}-${String(max + 1).padStart(4, '0')}`;
  }

  function dueDays(dueDateStr){
    const due = new Date(dueDateStr + 'T00:00:00');
    const today = new Date(); today.setHours(0,0,0,0);
    return Math.round((due - today) / 86400000);
  }

  /* Compact relative-day label: "6ago" if past, "Today" if today, "6d left" if upcoming. */
  function relativeDaysLabel(dateStr){
    const d = dueDays(dateStr);
    if (d < 0) return `${Math.abs(d)}ago`;
    if (d === 0) return 'Today';
    return `${d}d left`;
  }

  function ymd(dt){
    return `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, '0')}-${String(dt.getDate()).padStart(2, '0')}`;
  }

  /* Shared quick date-range calculator (today / this_week / last_month / this_month / next_month)
     used by the Invoices and Renewals quick-filter chips. Uses local Y-M-D formatting throughout
     (not toISOString, which shifts by the timezone offset and can land on the wrong day). */
  function dateRangeFor(key){
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    if (key === 'today') return { from: ymd(today), to: ymd(today) };
    if (key === 'this_week') {
      const day = today.getDay();
      const monday = new Date(today); monday.setDate(today.getDate() + (day === 0 ? -6 : 1 - day));
      const sunday = new Date(monday); sunday.setDate(monday.getDate() + 6);
      return { from: ymd(monday), to: ymd(sunday) };
    }
    if (key === 'last_month') {
      return { from: ymd(new Date(today.getFullYear(), today.getMonth() - 1, 1)), to: ymd(new Date(today.getFullYear(), today.getMonth(), 0)) };
    }
    if (key === 'this_month') {
      return { from: ymd(new Date(today.getFullYear(), today.getMonth(), 1)), to: ymd(new Date(today.getFullYear(), today.getMonth() + 1, 0)) };
    }
    if (key === 'next_month') {
      return { from: ymd(new Date(today.getFullYear(), today.getMonth() + 1, 1)), to: ymd(new Date(today.getFullYear(), today.getMonth() + 2, 0)) };
    }
    return { from: '', to: '' };
  }

  /* Builds the letterhead's right-hand block (doctype + GST + company + address + contact)
     for the given tax mode, skipping any field left blank in Settings entirely rather than
     rendering an empty "Label:" line. Shared by invoice-create.php's collapsed letterhead and
     invoice-preview.php/quotation-preview.php's printed letterhead. */
  function companyLetterheadHtml(gstType, docType) {
    const c = CompanyProfile.get(gstType);
    let html = `<div class="bill-doctype">${docType}</div>`;
    if (c.gstin) html += `<div class="fs-12 text-muted">GST : ${c.gstin}</div>`;
    if (c.name) html += `<div class="bl-company">${c.name}</div>`;
    if (c.address) html += `<div class="bl-meta">${c.address.replace(/\n/g, '<br>')}</div>`;
    const contact = [];
    if (c.phone) contact.push(`Mobile: ${c.phone}`);
    if (c.website) contact.push(c.website);
    if (contact.length) html += `<div class="bl-meta mt-1">${contact.join('<br>')}</div>`;
    return html;
  }

  return { DATA, qs, Auth, Masters, CompanyProfile, Notifications, Ledger, loadLayout, renderNotifDropdown, toast, showSkeleton, money, moneyShort, exportCsv, Charts, confirmDialog, initTooltips, makeResponsiveTable, nextInvoiceNumber, dueDays, relativeDaysLabel, dateRangeFor, companyLetterheadHtml };
})();

document.addEventListener('DOMContentLoaded', () => { KTG.loadLayout(); });
