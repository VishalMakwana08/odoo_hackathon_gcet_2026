<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: index.php?action=signin");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dayflow | Employee Dashboard</title>
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .user-badge {
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
        
        .user-badge i {
            font-size: 14px;
        }
        
        .user-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            box-shadow: var(--shadow-sm);
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
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-title i {
            font-size: 20px;
        }
        
        /* Enhanced Grid Layout */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .grid-full {
            grid-column: 1 / -1;
        }
        
        /* Attendance Widget */
        .attendance-widget {
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
            border: 1px solid #BAE6FD;
            border-radius: var(--radius);
            padding: 30px;
            text-align: center;
            transition: var(--transition);
        }
        
        .attendance-widget:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .time-display {
            font-size: 48px;
            font-weight: 800;
            color: var(--primary-dark);
            margin: 20px 0;
            font-feature-settings: "tnum";
        }
        
        .date-display {
            font-size: 16px;
            color: var(--text-light);
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 25px;
        }
        
        .status-present {
            background: var(--success-light);
            color: #065F46;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .status-absent {
            background: var(--danger-light);
            color: #991B1B;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .attendance-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        .btn-attendance {
            flex: 1;
            padding: 16px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-attendance:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .btn-clockin {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }
        
        .btn-clockout {
            background: linear-gradient(135deg, var(--danger) 0%, #DC2626 100%);
            color: white;
        }
        
        .btn-disabled {
            background: var(--border);
            color: var(--text-lighter);
            cursor: not-allowed;
        }
        
        /* Enhanced Forms */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
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
        
        .form-row {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
            line-height: 1.5;
        }
        
        /* Enhanced Tables */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: white;
            margin-top: 20px;
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
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Payroll Display */
        .payroll-card {
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .payroll-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }
        
        .payroll-body {
            padding: 0;
        }
        
        .payroll-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-light);
            transition: var(--transition);
        }
        
        .payroll-row:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        .payroll-row:last-child {
            border-bottom: none;
        }
        
        .payroll-label {
            font-weight: 500;
            color: var(--text-light);
        }
        
        .payroll-value {
            font-weight: 600;
            color: var(--text);
        }
        
        .payroll-total {
            background: var(--bg);
            font-weight: 800;
            color: var(--primary);
        }
        
        .payroll-deduction {
            color: var(--danger);
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
        
        /* Profile Card */
        .profile-card {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-light);
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 32px;
            box-shadow: var(--shadow);
        }
        
        .profile-info h4 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .profile-info p {
            color: var(--text-light);
            font-size: 14px;
        }
        
        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .detail-item {
            background: var(--bg);
            padding: 20px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-light);
        }
        
        .detail-label {
            font-size: 12px;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
        }
        
        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: var(--text-light);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h4 {
            color: var(--text-light);
            margin-bottom: 8px;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }
            
            .workspace {
                padding: 24px;
            }
            
            .grid {
                grid-template-columns: 1fr;
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
            
            .user-info {
                gap: 12px;
            }
            
            .user-badge span {
                display: none;
            }
            
            .workspace {
                padding: 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .attendance-actions {
                flex-direction: column;
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
            <a href="javascript:void(0)" class="nav-link active" data-module="dashboard" onclick="loadDashboard()">
                <i>📊</i>
                <span>Dashboard</span>
            </a>
            <a href="javascript:void(0)" class="nav-link" data-module="profile" onclick="loadModule('profile')">
                <i>👤</i>
                <span>My Profile</span>
            </a>
            <a href="javascript:void(0)" class="nav-link" data-module="attendance" onclick="loadModule('attendance')">
                <i>📅</i>
                <span>Attendance</span>
            </a>
            <a href="javascript:void(0)" class="nav-link" data-module="payroll" onclick="loadModule('payroll')">
                <i>💰</i>
                <span>My Payroll</span>
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
                <span>Dayflow</span>
                <span class="breadcrumb-separator">/</span>
                <span id="breadCrumb">Dashboard</span>
            </div>
            <div class="user-info">
                <div class="user-badge">
                    <i>👤</i>
                    <span>Employee</span>
                </div>
                <div class="user-avatar">
                    E
                </div>
            </div>
        </div>

        <div class="workspace" id="moduleContainer">
            <div class="grid">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i>⏰</i>
                            <span>Daily Attendance</span>
                        </div>
                    </div>
                    <div class="attendance-widget" id="att-area">
                        <div class="date-display" id="currentDate"></div>
                        <div class="time-display" id="currentTime"></div>
                        <div class="status-badge status-absent" id="attStatus">Checking...</div>
                        <div class="attendance-actions" id="attActions">
                            <button class="btn-attendance btn-clockin" onclick="markAttendance('clock_in', this)" disabled>
                                <i>✓</i> Clock In
                            </button>
                            <button class="btn-attendance btn-clockout btn-disabled" onclick="markAttendance('clock_out', this)" disabled>
                                <i>⏱️</i> Clock Out
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i>🍃</i>
                            <span>Request Leave</span>
                        </div>
                    </div>
                    <form id="leaveForm">
                        <div class="form-group">
                            <label class="form-label">
                                <i>📋</i>
                                <span>Leave Category</span>
                            </label>
                            <select name="leave_type" class="form-control" required>
                                <option value="">Select leave type</option>
                                <option value="Paid">Paid Leave</option>
                                <option value="Sick">Sick Leave</option>
                                <option value="Unpaid">Unpaid Leave</option>
                                <option value="Emergency">Emergency Leave</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">
                                    <i>📅</i>
                                    <span>From Date</span>
                                </label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="form-col">
                                <label class="form-label">
                                    <i>📅</i>
                                    <span>To Date</span>
                                </label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i>📝</i>
                                <span>Reason</span>
                            </label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Please describe your reason for leave..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i>✈️</i>
                            <span>Submit Leave Request</span>
                        </button>
                    </form>
                </div>

                <div class="card grid-full">
                    <div class="card-header">
                        <div class="card-title">
                            <i>📋</i>
                            <span>Recent Leave Requests</span>
                        </div>
                        <button class="btn-primary" style="width: auto; padding: 10px 20px;" onclick="loadLeaveHistory()">
                            <i>↻</i>
                            <span>Refresh</span>
                        </button>
                    </div>
                    <div id="leave-history" class="table-container">
                        <div class="empty-state">
                            <i>📋</i>
                            <h4>Loading your leave history...</h4>
                            <p>Please wait while we fetch your records</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Update current time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit',
            hour12: false 
        });
    }
    
    // Update time every second
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // AJAX for Leave Application
    document.getElementById('leaveForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span>Processing...</span>';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        formData.append('action', 'apply_leave');
        
        fetch('api/employee_api.php', { 
            method: 'POST', 
            body: formData 
        })
        .then(res => res.text())
        .then(data => { 
            showNotification(data, 'success');
            this.reset();
            loadLeaveHistory();
        })
        .catch(error => {
            showNotification('Failed to submit leave request', 'danger');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Attendance Management
    function loadAttendance() {
        const attArea = document.getElementById('att-area');
        const attStatus = document.getElementById('attStatus');
        const attActions = document.getElementById('attActions');
        
        attStatus.textContent = 'Loading...';
        attStatus.className = 'status-badge';
        attActions.innerHTML = '<button class="btn-attendance btn-disabled" disabled>Loading...</button>';
        
        fetch('api/employee_api.php?action=get_att')
        .then(res => res.text())
        .then(data => { 
            attArea.innerHTML = data;
            // Normalize any inline markAttendance onclicks returned by server
            try {
                attArea.querySelectorAll('[onclick]').forEach(el => {
                    const oc = el.getAttribute('onclick') || '';
                    if (oc.includes('markAttendance')) {
                        const m = oc.match(/markAttendance\(['\"](\w+?)['\"](?:,\s*this)?\)/);
                        if (m) {
                            const type = m[1];
                            el.removeAttribute('onclick');
                            el.addEventListener('click', function(e){ markAttendance(type, this); });
                        }
                    }
                });
            } catch(e){}
        })
        .catch(error => {
            attStatus.textContent = 'Error loading';
            attStatus.className = 'status-badge status-absent';
            attActions.innerHTML = '<button class="btn-attendance btn-disabled" disabled>Service Unavailable</button>';
        });
    }

    function markAttendance(type, el) {
        // Determine button element reliably (supports onclick="markAttendance('type', this)")
        let btn = null;
        if (el && el.tagName) btn = el;
        else if (window.event && window.event.target) btn = window.event.target;
        else btn = document.activeElement;

        if (!btn) return showNotification('Unable to determine action button', 'danger');

        const originalText = btn.innerHTML;
        btn.innerHTML = '<i>⏳</i> Processing...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('action', type);

        fetch('api/employee_api.php', { 
            method: 'POST', 
            body: formData 
        })
        .then(res => res.text())
        .then(data => { 
            showNotification(data, 'success');
            loadAttendance();
        })
        .catch(error => {
            showNotification('Failed to record attendance', 'danger');
        })
        .finally(() => {
            try { btn.innerHTML = originalText; btn.disabled = false; } catch(e){}
        });
    }

    function loadLeaveHistory() {
        const historyContainer = document.getElementById('leave-history');
        historyContainer.innerHTML = `
            <div class="empty-state">
                <i>⏳</i>
                <h4>Loading leave history...</h4>
                <p>Please wait while we fetch your records</p>
            </div>
        `;
        
        fetch('api/employee_api.php?action=get_leaves')
        .then(res => res.text())
        .then(data => { 
            historyContainer.innerHTML = data; 
        })
        .catch(error => {
            historyContainer.innerHTML = `
                <div class="empty-state">
                    <i>⚠️</i>
                    <h4>Unable to load leave history</h4>
                    <p>Please try again later</p>
                    <button onclick="loadLeaveHistory()" class="btn-primary" style="width: auto; margin-top: 20px;">
                        <i>↻</i>
                        Retry
                    </button>
                </div>
            `;
        });
    }

    // Module Loader
    function loadDashboard() {
        document.getElementById('breadCrumb').innerText = 'Dashboard';
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector('.sidebar a:first-child').classList.add('active');
        
        document.getElementById('moduleContainer').innerHTML = `
            <div class="grid">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i>⏰</i>
                            <span>Daily Attendance</span>
                        </div>
                    </div>
                    <div class="attendance-widget" id="att-area">
                        <div class="date-display">Loading...</div>
                        <div class="time-display">--:--:--</div>
                        <div class="status-badge">Loading...</div>
                        <div class="attendance-actions">
                            <button class="btn-attendance btn-disabled" disabled>Loading...</button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i>🍃</i>
                            <span>Request Leave</span>
                        </div>
                    </div>
                    <form id="leaveForm">
                        <div class="form-group">
                            <label class="form-label">
                                <i>📋</i>
                                <span>Leave Category</span>
                            </label>
                            <select name="leave_type" class="form-control" required>
                                <option value="">Select leave type</option>
                                <option value="Paid">Paid Leave</option>
                                <option value="Sick">Sick Leave</option>
                                <option value="Unpaid">Unpaid Leave</option>
                                <option value="Emergency">Emergency Leave</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <label class="form-label">
                                    <i>📅</i>
                                    <span>From Date</span>
                                </label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="form-col">
                                <label class="form-label">
                                    <i>📅</i>
                                    <span>To Date</span>
                                </label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i>📝</i>
                                <span>Reason</span>
                            </label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Please describe your reason for leave..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i>✈️</i>
                            <span>Submit Leave Request</span>
                        </button>
                    </form>
                </div>

                <div class="card grid-full">
                    <div class="card-header">
                        <div class="card-title">
                            <i>📋</i>
                            <span>Recent Leave Requests</span>
                        </div>
                    </div>
                    <div id="leave-history" class="table-container">
                        <div class="empty-state">
                            <i>📋</i>
                            <h4>Loading your leave history...</h4>
                            <p>Please wait while we fetch your records</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Re-attach event listeners
        document.getElementById('leaveForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span>Processing...</span>';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            formData.append('action', 'apply_leave');
            
            fetch('api/employee_api.php', { 
                method: 'POST', 
                body: formData 
            })
            .then(res => res.text())
            .then(data => { 
                showNotification(data, 'success');
                this.reset();
                loadLeaveHistory();
            })
            .catch(error => {
                showNotification('Failed to submit leave request', 'danger');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
        
        loadAttendance();
        loadLeaveHistory();
    }

    function loadModule(module) {
        const mainView = document.getElementById('moduleContainer');
        const breadCrumb = document.getElementById('breadCrumb');
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        const moduleTitles = {
            'profile': 'My Profile',
            'attendance': 'Attendance History',
            'payroll': 'My Payroll'
        };
        
        breadCrumb.innerText = moduleTitles[module] || module;
        
        // Set active nav link using data-module attribute (safer)
        const activeLink = document.querySelector(`.nav-link[data-module="${module}"]`);
        if (activeLink) activeLink.classList.add('active');

        // Show loading state
        mainView.innerHTML = `
            <div class="card" style="text-align: center; padding: 60px;">
                <div style="font-size: 48px; margin-bottom: 20px; color: var(--primary);">⏳</div>
                <h3>Loading ${moduleTitles[module] || module}...</h3>
                <p style="color: var(--text-light);">Please wait while we fetch your data</p>
            </div>
        `;

        if (module === 'profile') {
            fetch('api/employee_api.php?action=get_profile')
                .then(res => res.json())
                .then(data => {
                    mainView.innerHTML = `
                        <div class="card profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar">E</div>
                                <div class="profile-info">
                                    <h4>Employee Profile</h4>
                                    <p>ID: ${data.employee_id || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="profile-details">
                                <div class="detail-item">
                                    <div class="detail-label">Employee ID</div>
                                    <div class="detail-value">${data.employee_id || 'N/A'}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Email Address</div>
                                    <div class="detail-value">${data.email || 'N/A'}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Role</div>
                                    <div class="detail-value">
                                        <span class="badge" style="background:#DBEAFE; color:#1E40AF; display:inline-block">${data.role || 'Employee'}</span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Member Since</div>
                                    <div class="detail-value">${data.created_at || 'N/A'}</div>
                                </div>
                            </div>
                            <div style="margin-top: 30px; padding: 20px; background: var(--bg); border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                                <p style="color: var(--text-light); font-size: 13px; margin: 0;">
                                    <i>ℹ️</i> For any profile corrections or updates, please contact the HR Department.
                                </p>
                            </div>
                        </div>`;
                })
                .catch(error => {
                    mainView.innerHTML = `
                        <div class="card" style="text-align: center; padding: 60px; border-color: var(--danger);">
                            <div style="font-size: 48px; margin-bottom: 20px; color: var(--danger);">⚠️</div>
                            <h3>Unable to load profile</h3>
                            <p style="color: var(--text-light);">Please try again later</p>
                            <button onclick="loadModule('profile')" class="btn-primary" style="width: auto; margin-top: 20px;">
                                <i>↻</i> Retry
                            </button>
                        </div>
                    `;
                });
        } else if (module === 'attendance') {
            fetch('api/employee_api.php?action=get_attendance_history')
                .then(res => res.text())
                .then(data => {
                    mainView.innerHTML = `
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i>📊</i>
                                    <span>Attendance History</span>
                                </div>
                                <button onclick="loadModule('attendance')" class="btn-primary" style="width: auto; padding: 10px 20px;">
                                    <i>↻</i>
                                    <span>Refresh</span>
                                </button>
                            </div>
                            <div class="table-container">
                                ${data}
                            </div>
                        </div>`;
                })
                .catch(error => {
                    mainView.innerHTML = `
                        <div class="card" style="text-align: center; padding: 60px;">
                            <div style="font-size: 48px; margin-bottom: 20px; color: var(--warning);">⚠️</div>
                            <h3>Unable to load attendance history</h3>
                            <p style="color: var(--text-light);">Please try again later</p>
                        </div>
                    `;
                });
        } else if (module === 'payroll') {
            fetch('api/employee_api.php?action=get_payroll')
            .then(res => res.json())
            .then(data => {
                if(data.message) {
                    mainView.innerHTML = `
                        <div class="card" style="max-width: 500px; margin: 0 auto;">
                            <div class="card-header">
                                <div class="card-title">
                                    <i>💰</i>
                                    <span>Payroll Details</span>
                                </div>
                            </div>
                            <div class="empty-state">
                                <i>📊</i>
                                <h4>${data.message}</h4>
                                <p>Payroll information will be available here</p>
                            </div>
                        </div>`;
                    return;
                }
                
                const netSalary = (parseInt(data.basic_salary) + parseInt(data.hra) + parseInt(data.allowances) - parseInt(data.deductions)).toLocaleString();
                
                mainView.innerHTML = `
                    <div class="payroll-card">
                        <div class="payroll-header">
                            <h3 style="margin: 0; font-size: 20px;">Salary Breakdown</h3>
                            <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">Monthly Compensation Details</p>
                        </div>
                        <div class="payroll-body">
                            <div class="payroll-row">
                                <span class="payroll-label">Basic Salary</span>
                                <span class="payroll-value">₹${parseInt(data.basic_salary).toLocaleString()}</span>
                            </div>
                            <div class="payroll-row">
                                <span class="payroll-label">HRA Allowance</span>
                                <span class="payroll-value">₹${parseInt(data.hra).toLocaleString()}</span>
                            </div>
                            <div class="payroll-row">
                                <span class="payroll-label">Other Allowances</span>
                                <span class="payroll-value">₹${parseInt(data.allowances).toLocaleString()}</span>
                            </div>
                            <div class="payroll-row payroll-deduction">
                                <span class="payroll-label">Deductions</span>
                                <span class="payroll-value">- ₹${parseInt(data.deductions).toLocaleString()}</span>
                            </div>
                            <div class="payroll-row payroll-total">
                                <span class="payroll-label">Net Salary</span>
                                <span class="payroll-value">₹${netSalary}</span>
                            </div>
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 20px; color: var(--text-light); font-size: 13px;">
                        <p><i>ℹ️</i> Salary details are updated at the beginning of each month</p>
                    </div>`;
            })
            .catch(error => {
                mainView.innerHTML = `
                    <div class="card" style="text-align: center; padding: 60px; max-width: 500px; margin: 0 auto;">
                        <div style="font-size: 48px; margin-bottom: 20px; color: var(--danger);">⚠️</div>
                        <h3>Unable to load payroll</h3>
                        <p style="color: var(--text-light);">Please contact HR for salary details</p>
                    </div>
                `;
            });
        }
    }

    // Notification System
    function showNotification(message, type = 'info') {
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

    // Initialize
    loadAttendance();
    loadLeaveHistory();
    
    // Auto-refresh attendance every 30 seconds
    setInterval(loadAttendance, 30000);
    </script>
</body>
</html>