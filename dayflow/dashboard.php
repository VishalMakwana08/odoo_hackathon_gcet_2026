<?php
session_start();
require_once 'db.php';
// Auth Guard: Admin access check [cite: 9, 13]
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?action=signin");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dayflow Admin | Management Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --primary-light: #3B82F6;
            --primary-dark: #1D4ED8;
            --bg: #F8FAFC;
            --card: #FFFFFF;
            --text: #1E293B;
            --text-light: #64748B;
            --text-lighter: #94A3B8;
            --border: #E2E8F0;
            --border-light: #F1F5F9;
            --success: #10B981;
            --success-light: #D1FAE5;
            --warning: #F59E0B;
            --warning-light: #FEF3C7;
            --danger: #EF4444;
            --danger-light: #FEE2E2;
            --sidebar: #0F172A;
            --sidebar-hover: #1E293B;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 16px;
            --transition: all 0.2s ease-in-out;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Enhanced Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 30px 0;
            box-sizing: border-box;
            flex-shrink: 0;
            box-shadow: var(--shadow-md);
            z-index: 10;
        }
        
        .sidebar-header {
            padding: 0 25px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        
        .sidebar h2 {
            color: var(--primary-light);
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, var(--primary-light) 0%, #60A5FA 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-container {
            padding: 0 15px;
            flex: 1;
        }
        
        .nav-link {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            color: var(--text-lighter);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            background: var(--sidebar-hover);
            color: white;
            transform: translateX(3px);
        }
        
        .nav-link.active {
            background: var(--sidebar-hover);
            color: white;
            border-left: 3px solid var(--primary);
            font-weight: 600;
            box-shadow: inset 2px 0 10px rgba(59, 130, 246, 0.1);
        }
        
        .nav-link i {
            margin-right: 12px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }
        
        .logout-link {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin: 20px 15px 0;
        }
        
        /* Enhanced Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            background: var(--bg);
        }
        
        .top-bar {
            background: white;
            padding: 20px 32px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 5;
        }
        
        .breadcrumb {
            font-weight: 500;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .breadcrumb-separator {
            color: var(--text-lighter);
            font-size: 12px;
        }
        
        .admin-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--shadow-sm);
        }
        
        .admin-badge i {
            font-size: 14px;
        }
        
        .workspace {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Enhanced Cards */
        .card {
            background: var(--card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 28px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-light);
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }
        
        /* Enhanced Stats Grid */
        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            padding: 24px;
            border-radius: var(--radius);
            border: 1px solid transparent;
            background: white;
            transition: var(--transition);
            cursor: pointer;
            box-shadow: var(--shadow-sm);
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .stat-label {
            font-size: 12px;
            color: var(--text-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--text);
            margin: 8px 0;
        }
        
        .stat-trend {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
        }
        
        /* Enhanced Tables */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: white;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }
        
        thead {
            background: var(--bg);
        }
        
        th {
            text-align: left;
            font-size: 12px;
            color: var(--text-light);
            padding: 18px 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            border-bottom: 2px solid var(--border-light);
            white-space: nowrap;
        }
        
        tbody tr {
            transition: var(--transition);
        }
        
        tbody tr:hover {
            background: var(--bg);
        }
        
        td {
            padding: 18px 20px;
            font-size: 14px;
            background: transparent;
            border-bottom: 1px solid var(--border-light);
            font-weight: 500;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Enhanced Badges */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .badge i {
            font-size: 10px;
        }
        
        .Pending {
            background: var(--warning-light);
            color: #92400E;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        
        .Approved {
            background: var(--success-light);
            color: #065F46;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .Rejected {
            background: var(--danger-light);
            color: #991B1B;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        /* Enhanced Buttons */
        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            margin-right: 8px;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-action i {
            font-size: 12px;
        }
        
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        .btn-approve {
            background: var(--success);
            color: white;
        }
        
        .btn-approve:hover {
            background: #059669;
        }
        
        .btn-reject {
            background: var(--danger);
            color: white;
        }
        
        .btn-reject:hover {
            background: #DC2626;
        }
        
        .btn-secondary {
            background: #64748B;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #475569;
        }
        
        .btn-icon {
            padding: 8px;
            border-radius: var(--radius-sm);
            background: var(--bg);
            border: 1px solid var(--border);
            cursor: pointer;
            color: var(--text-light);
            transition: var(--transition);
        }
        
        .btn-icon:hover {
            background: var(--border);
            color: var(--text);
        }
        
        /* Enhanced Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            animation: fadeInModal 0.2s ease-out;
        }
        
        @keyframes fadeInModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            background: white;
            margin: 8% auto;
            padding: 40px;
            border-radius: var(--radius-lg);
            width: 480px;
            max-width: 90%;
            box-shadow: var(--shadow-lg);
            animation: slideInModal 0.3s ease-out;
        }
        
        @keyframes slideInModal {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .modal-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }
        
        .modal-subtitle {
            color: var(--text-light);
            font-size: 14px;
        }
        
        /* Enhanced Form Elements */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 8px;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        .btn-cancel {
            background: none;
            border: none;
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            cursor: pointer;
            color: var(--text-light);
            font-size: 13px;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-cancel:hover {
            color: var(--text);
        }
        
        /* Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .loading::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-left: 8px;
            vertical-align: middle;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 32px;
        }
        
        .welcome-card h3 {
            color: white;
            font-size: 24px;
            margin-bottom: 12px;
        }
        
        .welcome-card p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 15px;
            max-width: 600px;
            line-height: 1.6;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }
            
            .workspace {
                padding: 24px;
            }
            
            .grid-stats {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 20px 0;
            }
            
            .sidebar-header, .nav-link span {
                display: none;
            }
            
            .nav-link {
                justify-content: center;
                padding: 16px;
            }
            
            .nav-link i {
                margin-right: 0;
                font-size: 18px;
            }
            
            .top-bar {
                padding: 16px 20px;
            }
            
            .workspace {
                padding: 20px;
            }
            
            .grid-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Dayflow</h2>
        </div>
        <div class="nav-container">
            <a class="nav-link active" onclick="loadAdminModule('dashboard', this)">
                <i>📊</i>
                <span>Admin Overview</span>
            </a>
            <a class="nav-link" onclick="loadAdminModule('employees', this)">
                <i>👥</i>
                <span>Employee Directory</span>
            </a>
            <a class="nav-link" onclick="loadAdminModule('attendance', this)">
                <i>📅</i>
                <span>Attendance Logs</span>
            </a>
            <a class="nav-link" onclick="loadAdminModule('leaves', this)">
                <i>🍃</i>
                <span>Leave Requests</span>
            </a>
            <a class="nav-link" onclick="loadAdminModule('payroll', this)">
                <i>💰</i>
                <span>Payroll Control</span>
            </a>
            <a href="auth/logout.php" class="nav-link logout-link">
                <i>🚪</i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="breadcrumb">
                <span>Admin</span>
                <span class="breadcrumb-separator">/</span>
                <span id="breadCrumb">Overview</span>
            </div>
            <div class="admin-badge">
                <i>👑</i>
                <span>HR Administrator</span>
            </div>
        </div>

        <div class="workspace" id="adminContainer">
            <div class="grid-stats">
                <div class="stat-card" style="border-color: #DBEAFE; border-left: 4px solid #3B82F6;">
                    <div class="stat-label">
                        <i>👥</i>
                        <span>TOTAL EMPLOYEES</span>
                    </div>
                    <div class="stat-value" id="count-emp">...</div>
                    <div class="stat-trend" style="color: #3B82F6;">
                        <i>↗</i>
                        <span>Active workforce</span>
                    </div>
                </div>
                <div class="stat-card" style="border-color: #D1FAE5; border-left: 4px solid #10B981;">
                    <div class="stat-label">
                        <i>✓</i>
                        <span>TODAY'S ATTENDANCE</span>
                    </div>
                    <div class="stat-value" id="count-att">...</div>
                    <div class="stat-trend" style="color: #10B981;">
                        <i>↗</i>
                        <span>Present today</span>
                    </div>
                </div>
                <div class="stat-card" style="border-color: #FEF3C7; border-left: 4px solid #F59E0B;">
                    <div class="stat-label">
                        <i>⏳</i>
                        <span>PENDING LEAVES</span>
                    </div>
                    <div class="stat-value" id="count-leave">...</div>
                    <div class="stat-trend" style="color: #F59E0B;">
                        <i>!</i>
                        <span>Requires action</span>
                    </div>
                </div>
            </div>
            <div class="card welcome-card">
                <h3>Welcome back, Administrator 👋</h3>
                <p>Manage your HR operations efficiently. Monitor attendance, process leave requests, and handle payroll all from one dashboard.</p>
            </div>
        </div>
    </div>

    <div id="payrollModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Update Salary Structure</div>
                <div class="modal-subtitle">Adjust compensation details for the selected employee</div>
            </div>
            <form id="payrollForm">
                <input type="hidden" name="user_id" id="modal_uid">
                <div class="form-group">
                    <label class="form-label">Basic Salary (₹)</label>
                    <input type="number" name="basic" id="modal_basic" class="form-control" placeholder="Enter basic salary" required>
                </div>
                <div class="form-group">
                    <label class="form-label">HRA Allowance (₹)</label>
                    <input type="number" name="hra" id="modal_hra" class="form-control" placeholder="Enter HRA amount">
                </div>
                <div class="form-group">
                    <label class="form-label">Deductions (₹)</label>
                    <input type="number" name="deductions" id="modal_deduct" class="form-control" placeholder="Enter deductions">
                </div>
                <button type="button" onclick="savePayroll()" class="btn-primary">
                    <span id="saveBtnText">Save Changes</span>
                </button>
                <button type="button" onclick="closeModal()" class="btn-cancel">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    function loadAdminModule(module, element) {
        // Update active navigation
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        if(element) element.classList.add('active');
        
        // Update breadcrumb
        const moduleNames = {
            'dashboard': 'Overview',
            'employees': 'Employee Directory',
            'attendance': 'Attendance Logs',
            'leaves': 'Leave Requests',
            'payroll': 'Payroll Control'
        };
        document.getElementById('breadCrumb').innerText = moduleNames[module] || module.toUpperCase();
        
        // Show loading state
        const container = document.getElementById('adminContainer');
        container.classList.add('loading');
        container.innerHTML = `
            <div class="card" style="text-align: center; padding: 60px;">
                <div style="font-size: 48px; margin-bottom: 20px;">⏳</div>
                <h3>Loading ${moduleNames[module] || module}...</h3>
                <p style="color: var(--text-light);">Fetching the latest data</p>
            </div>
        `;
        
        // Fetch module content
        fetch(`api/admin_api.php?module=${module}`)
        .then(res => res.text())
        .then(data => { 
            container.innerHTML = `<div class="card"><div class="card-header"><div class="card-title">${moduleNames[module] || module.toUpperCase()} MANAGEMENT</div></div>${data}</div>`; 
            container.classList.remove('loading');
        })
        .catch(error => {
            container.innerHTML = `
                <div class="card" style="text-align: center; padding: 60px; border-color: var(--danger);">
                    <div style="font-size: 48px; margin-bottom: 20px; color: var(--danger);">⚠️</div>
                    <h3>Failed to load data</h3>
                    <p style="color: var(--text-light);">Please try again</p>
                    <button onclick="loadAdminModule('${module}')" class="btn-action btn-secondary" style="margin-top: 20px;">
                        <i>↻</i> Retry
                    </button>
                </div>
            `;
            container.classList.remove('loading');
        });
    }

    function updateStats() {
        const stats = ['count-emp', 'count-att', 'count-leave'];
        stats.forEach(id => {
            const el = document.getElementById(id);
            el.innerHTML = '<span style="opacity: 0.5">...</span>';
        });
        
        fetch('api/admin_api.php?module=dashboard_counts')
        .then(res => res.json())
        .then(data => {
            document.getElementById('count-emp').innerText = data.employees;
            document.getElementById('count-att').innerText = data.attendance;
            document.getElementById('count-leave').innerText = data.leaves;
        })
        .catch(() => {
            stats.forEach(id => {
                document.getElementById(id).innerText = '--';
            });
        });
    }

    function manageLeave(id, status) {
        if(!confirm(`Are you sure you want to ${status.toLowerCase()} this leave request?`)) return;
        
        const f = new FormData(); 
        f.append('leave_id', id); 
        f.append('status', status); 
        f.append('action', 'manage_leave');
        
        const btn = event?.target || document.activeElement;
        const originalText = btn.innerHTML;
        btn.classList.add('loading');
        btn.disabled = true;
        
        fetch('api/admin_api.php', { method:'POST', body:f })
        .then(res => res.text())
        .then(d => { 
            showNotification(d, status === 'Approved' ? 'success' : 'warning');
            loadAdminModule('leaves'); 
        })
        .catch(() => {
            showNotification('Failed to process request', 'danger');
        })
        .finally(() => {
            btn.classList.remove('loading');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

 // --- UPDATED MODAL FUNCTION ---
    function openPayrollModal(uid) {
        // Show loading in fields before data arrives
        document.getElementById('modal_basic').placeholder = 'Loading current data...';
        document.getElementById('modal_uid').value = uid;
        
        // Fetch existing payroll data
        fetch(`api/admin_api.php?module=get_payroll_details&user_id=${uid}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('modal_basic').value = data.basic_salary || '';
            document.getElementById('modal_hra').value = data.hra || '';
            document.getElementById('modal_deduct').value = data.deductions || '';
            
            document.getElementById('payrollModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        })
        .catch(() => {
            showNotification('Failed to fetch existing data', 'danger');
            document.getElementById('payrollModal').style.display = 'block';
        });
    }
    
    // --- REST OF THE CODE SAME AS YOURS ---
    function closeModal() {
        document.getElementById('payrollModal').style.display='none';
        document.body.style.overflow = 'auto';
        document.getElementById('payrollForm').reset();
    }
    

    
    function savePayroll() {
        const saveBtn = document.querySelector('#payrollModal .btn-primary');
        const originalText = saveBtn.innerHTML;
        saveBtn.classList.add('loading');
        saveBtn.disabled = true;
        
        const formData = new FormData(document.getElementById('payrollForm'));
        formData.append('action', 'set_salary');
        
        fetch('api/admin_api.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => { 
            showNotification(data, 'success');
            closeModal(); 
            loadAdminModule('payroll'); 
        })
        .catch(() => {
            showNotification('Failed to save changes', 'danger');
        })
        .finally(() => {
            saveBtn.classList.remove('loading');
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        });
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: var(--radius);
            background: white;
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--${type});
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
            max-width: 350px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        `;
        
        const icons = {
            success: '✅',
            warning: '⚠️',
            danger: '❌',
            info: 'ℹ️'
        };
        
        notification.innerHTML = `
            <span style="font-size: 18px;">${icons[type] || 'ℹ️'}</span>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('payrollModal');
        if (event.target === modal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // Initialize
    updateStats();
    setInterval(updateStats, 30000); // Auto-refresh stats every 30 seconds
    </script>
</body>
</html>