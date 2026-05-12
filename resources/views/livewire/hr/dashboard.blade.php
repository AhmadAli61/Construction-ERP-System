{{-- resources/views/livewire/hr/dashboard.blade.php --}}
<div >    
    <!-- MAIN CARD WRAPPER - THIS IS THE ONLY ADDITION -->
    <div style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
    
    <style>,
            @keyframes float {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
            }
            25% {
                transform: translateY(-20px) translateX(10px);
            }
            50% {
                transform: translateY(10px) translateX(-15px);
            }
            75% {
                transform: translateY(-10px) translateX(15px);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }
        
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .dashboard-card {
            animation: fadeInUp 0.6s ease-out;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }
        
        .clickable-card {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .clickable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }
        
        .stat-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .quick-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .quick-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .quick-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .progress-ring {
            transition: stroke-dashoffset 0.5s ease;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .hover-scale {
            transition: transform 0.3s ease;
        }
        
        .hover-scale:hover {
            transform: scale(1.02);
        }
        
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
        }
        
        .progress-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .table-row {
            transition: all 0.2s ease;
        }
        
        .table-row:hover {
            background: #f8fafc;
            transform: scale(1.01);
        }
        
        .badge {
            transition: all 0.2s ease;
        }
        
        .badge:hover {
            transform: scale(1.05);
        }
    </style>
        <!-- Header Section with Gradient - Enhanced Background Designs -->
    <div style="background: linear-gradient(135deg, #ff0000 0%, #8b0000 50%, #000000 100%); padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); position: relative; overflow: hidden;">
        
        <!-- Animated Gradient Overlay -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.03) 50%, transparent 70%); background-size: 200% 200%; animation: shimmer 3s ease infinite;"></div>
        
        <!-- Large Circles -->
        <div style="position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.15), rgba(255,255,255,0)); border-radius: 50%; animation: float 20s ease-in-out infinite;"></div>
        <div style="position: absolute; bottom: -30%; left: -5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1), rgba(255,255,255,0)); border-radius: 50%; animation: float 15s ease-in-out infinite reverse;"></div>
        <div style="position: absolute; top: 20%; left: 20%; width: 150px; height: 150px; background: radial-gradient(circle, rgba(255,255,255,0.08), rgba(255,255,255,0)); border-radius: 50%; animation: pulse 8s ease-in-out infinite;"></div>
        
        <!-- Medium Circles -->
        <div style="position: absolute; top: 60%; right: 20%; width: 120px; height: 120px; background: radial-gradient(circle, rgba(255,255,255,0.12), rgba(255,255,255,0)); border-radius: 50%; animation: float 12s ease-in-out infinite 2s;"></div>
        <div style="position: absolute; bottom: 10%; right: 35%; width: 80px; height: 80px; background: radial-gradient(circle, rgba(255,255,255,0.1), rgba(255,255,255,0)); border-radius: 50%; animation: pulse 6s ease-in-out infinite 1s;"></div>
        <div style="position: absolute; top: 15%; left: 45%; width: 100px; height: 100px; background: radial-gradient(circle, rgba(255,255,255,0.06), rgba(255,255,255,0)); border-radius: 50%; animation: float 18s ease-in-out infinite;"></div>
        
        <!-- Small Decorative Circles -->
        <div style="position: absolute; top: 40%; right: 45%; width: 40px; height: 40px; background: rgba(255,255,255,0.08); border-radius: 50%; animation: pulse 4s ease-in-out infinite;"></div>
        <div style="position: absolute; bottom: 30%; left: 15%; width: 50px; height: 50px; background: rgba(255,255,255,0.05); border-radius: 50%; animation: float 10s ease-in-out infinite 1s;"></div>
        <div style="position: absolute; top: 75%; left: 50%; width: 25px; height: 25px; background: rgba(255,255,255,0.07); border-radius: 50%; animation: pulse 5s ease-in-out infinite 2s;"></div>
        <div style="position: absolute; top: 10%; right: 60%; width: 35px; height: 35px; background: rgba(255,255,255,0.06); border-radius: 50%; animation: float 14s ease-in-out infinite;"></div>
        <div style="position: absolute; bottom: 50%; right: 5%; width: 20px; height: 20px; background: rgba(255,255,255,0.09); border-radius: 50%; animation: pulse 3s ease-in-out infinite;"></div>
        
        <!-- Diagonal Lines Pattern -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0px, rgba(255,255,255,0.02) 2px, transparent 2px, transparent 8px); pointer-events: none;"></div>
        
        <!-- Dots Pattern -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 30px 30px; pointer-events: none;"></div>
        
        <!-- Glowing Orbs -->
        <div style="position: absolute; top: 50%; left: 10%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; filter: blur(20px);"></div>
        <div style="position: absolute; bottom: 20%; right: 15%; width: 180px; height: 180px; background: radial-gradient(circle, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; filter: blur(20px);"></div>
        
        <!-- Border Accent Lines -->
        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), rgba(255,255,255,0.6), rgba(255,255,255,0.3), transparent);"></div>
        <div style="position: absolute; top: 0; left: 0; width: 3px; height: 100%; background: linear-gradient(180deg, transparent, rgba(255,255,255,0.2), rgba(255,255,255,0.5), rgba(255,255,255,0.2), transparent);"></div>
        <div style="position: absolute; top: 0; right: 0; width: 3px; height: 100%; background: linear-gradient(180deg, transparent, rgba(255,255,255,0.2), rgba(255,255,255,0.5), rgba(255,255,255,0.2), transparent);"></div>
        
        <div style="display: flex; justify-content: space-between; align-items: stretch; flex-wrap: wrap; gap: 30px; position: relative; z-index: 1;">
            
            <!-- Left Content -->
            <div style="flex: 1.5;">
                <div style="display: flex; align-items: center; margin-bottom: 20px;">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 15px; padding: 8px 16px; margin-right: 15px; backdrop-filter: blur(5px);">
                        <i class="fas fa-chart-line" style="color: white; font-size: 16px;"></i>
                    </div>
                    <span style="background: white; padding: 8px 20px; border-radius: 25px; color: black; font-size: 14px; font-weight: 600; backdrop-filter: blur(5px);">HR Dashboard</span>
                </div>
                
                <h1 style="color: white; font-size: 42px; font-weight: 800; margin-bottom: 15px; letter-spacing: -1px; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                    Welcome to HR Dashboard
                </h1>
                <h2 style="color: rgba(255,255,255,0.95); font-size: 28px; font-weight: 600; margin-bottom: 20px;">
                    {{ auth()->user()->name }}
                </h2>
                <p style="color: rgba(255,255,255,0.85); font-size: 16px; max-width: 550px; margin-bottom: 25px; line-height: 1.5;">
                    Real-time workforce analytics, predictive insights, and performance metrics for strategic decision making.
                </p>
                
                <div style="display: flex; gap: 25px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.15); padding: 8px 18px; border-radius: 12px; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.1);">
                        <i class="fas fa-sync-alt" style="color: rgba(255,255,255,0.9); font-size: 14px;"></i>
                        <div>
                            <small style="color: rgba(255,255,255,0.7); display: block; font-size: 11px;">Last updated</small>
                            <small style="color: white; font-weight: 500;">{{ now()->format('h:i:s A') }}</small>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.15); padding: 8px 18px; border-radius: 12px; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.1);">
                        <i class="fas fa-calendar-alt" style="color: rgba(255,255,255,0.9); font-size: 14px;"></i>
                        <div>
                            <small style="color: rgba(255,255,255,0.7); display: block; font-size: 11px;">Current Date</small>
                            <small style="color: white; font-weight: 500;">{{ now()->format('F d, Y') }}</small>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.15); padding: 8px 18px; border-radius: 12px; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.1);">
                        <i class="fas fa-clock" style="color: rgba(255,255,255,0.9); font-size: 14px;"></i>
                        <div>
                            <small style="color: rgba(255,255,255,0.7); display: block; font-size: 11px;">Current Time</small>
                            <small style="color: white; font-weight: 500;" id="currentTime">{{ now()->format('h:i A') }}</small>
                        </div>
                    </div>
                </div>
                                <!-- Performance Indicators - Fills empty space -->
                <div style="background: white; border-radius: 15px; padding: 10px; margin-top: 20px; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.1); width: 500px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <i class="fas fa-chart-simple" style="color: #ff9999; font-size: 16px;"></i>
                        <span style="color: black; font-weight: 600; font-size: 13px;">Key Performance Indicators</span>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="color: rgba(57, 56, 56, 0.7); font-size: 11px;">Attendance Rate</span>
                                <span style="color: black; font-size: 11px; font-weight: 600;">{{ $quickStats['attendance_rate'] ?? 0 }}%</span>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); border-radius: 10px; height: 4px; overflow: hidden;">
                                <div style="background: #10b981; width: {{ $quickStats['attendance_rate'] ?? 0 }}%; height: 100%;"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="color: rgba(57, 56, 56, 0.7); font-size: 11px;">Medical Compliance</span>
                                <span style="color: black; font-size: 11px; font-weight: 600;">{{ $medicalStatus['valid_percentage'] ?? 0 }}%</span>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); border-radius: 10px; height: 4px; overflow: hidden;">
                                <div style="background: #f59e0b; width: {{ $medicalStatus['valid_percentage'] ?? 0 }}%; height: 100%;"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="color: rgba(57, 56, 56, 0.7); font-size: 11px;">Project Completion</span>
                                <span style="color: black; font-size: 11px; font-weight: 600;">{{ $projectStats['active_percentage'] ?? 0 }}%</span>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); border-radius: 10px; height: 4px; overflow: hidden;">
                                <div style="background: #3b82f6; width: {{ $projectStats['active_percentage'] ?? 0 }}%; height: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Content - White Background Stats Panels -->
            <div style="flex: 1; display: flex; flex-direction: column; gap: 15px;">
                
                <!-- Row 1: Two White Cards -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    <!-- TOTAL WORKERS CARD - Clickable to Workers List -->
                    <div onclick="window.location='{{ route('hr.workers.list') }}'" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';">
                        <div style="position: absolute; top: -50%; right: -50%; width: 100px; height: 100px; background: radial-gradient(circle, rgba(255,0,0,0.05), transparent); border-radius: 50%;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px; position: relative; z-index: 1;">
                            <div>
                                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 5px;">TOTAL WORKERS</p>
                                <h3 style="font-size: 36px; font-weight: 800; color: #1f2937; margin: 0;">{{ number_format($quickStats['total_workers']) }}</h3>
                            </div>
                            <div style="background: linear-gradient(135deg, #ff000020, #00000020); border-radius: 15px; padding: 10px;">
                                <i class="fas fa-users" style="color: #ff0000; font-size: 24px;"></i>
                            </div>
                        </div>
                        <div style="margin-top: 10px; position: relative; z-index: 1;">
                            <span style="color: #10b981; font-size: 13px; font-weight: 600;">✓ {{ $quickStats['active_workers'] }} Active</span>
                            <span style="color: #ef4444; font-size: 13px; font-weight: 600; margin-left: 10px;">✗ {{ $quickStats['inactive_workers'] }} Inactive</span>
                        </div>
                    </div>
                    
                    <!-- ACTIVE PROJECTS CARD - Clickable to Projects List -->
                    <div onclick="window.location='{{ route('hr.projects.list') }}'" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';">
                        <div style="position: absolute; top: -50%; right: -50%; width: 100px; height: 100px; background: radial-gradient(circle, rgba(16,185,129,0.05), transparent); border-radius: 50%;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px; position: relative; z-index: 1;">
                            <div>
                                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 5px;">ACTIVE PROJECTS</p>
                                <h3 style="font-size: 36px; font-weight: 800; color: #1f2937; margin: 0;">{{ number_format($quickStats['active_projects']) }}</h3>
                            </div>
                            <div style="background: linear-gradient(135deg, #10b98120, #05966920); border-radius: 15px; padding: 10px;">
                                <i class="fas fa-project-diagram" style="color: #10b981; font-size: 24px;"></i>
                            </div>
                        </div>
                        <div style="margin-top: 10px; position: relative; z-index: 1;">
                            <span style="color: #3b82f6; font-size: 13px; font-weight: 600;">✓ {{ $quickStats['completed_projects'] }} Completed</span>
                            <span style="color: #f59e0b; font-size: 13px; font-weight: 600; margin-left: 10px;">📋 {{ $quickStats['planning_projects'] }} Planning</span>
                        </div>
                    </div>
                </div>
                
                <!-- Row 2: Two White Cards -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    <!-- ATTENDANCE RATE CARD - Clickable to Attendance Sheet -->
                    <div onclick="window.location='{{ route('hr.attendancesheet') }}'" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';">
                        <div style="position: absolute; top: -50%; right: -50%; width: 100px; height: 100px; background: radial-gradient(circle, rgba(245,158,11,0.05), transparent); border-radius: 50%;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px; position: relative; z-index: 1;">
                            <div>
                                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 5px;">ATTENDANCE RATE</p>
                                <h3 style="font-size: 36px; font-weight: 800; color: #1f2937; margin: 0;">{{ $quickStats['attendance_rate'] }}%</h3>
                            </div>
                            <div style="background: linear-gradient(135deg, #f59e0b20, #d9770620); border-radius: 15px; padding: 10px;">
                                <i class="fas fa-calendar-check" style="color: #f59e0b; font-size: 24px;"></i>
                            </div>
                        </div>
                        <div style="margin-top: 10px; position: relative; z-index: 1;">
                            <div style="background: #e5e7eb; border-radius: 10px; height: 6px; overflow: hidden;">
                                <div style="background: linear-gradient(135deg, #f59e0b, #d97706); width: {{ $quickStats['attendance_rate'] }}%; height: 100%;"></div>
                            </div>
                            <p style="color: #6b7280; font-size: 12px; margin-top: 8px;">{{ $quickStats['attendance_days'] }} / {{ $quickStats['total_attendance_days'] }} days</p>
                        </div>
                    </div>
                    
                    <!-- MEDICAL COMPLIANCE CARD - Clickable to Workers List (Medical Tab) -->
                    <div onclick="window.location='{{ route('hr.workers.list') }}?tab=medical'" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';">
                        <div style="position: absolute; top: -50%; right: -50%; width: 100px; height: 100px; background: radial-gradient(circle, rgba(239,68,68,0.05), transparent); border-radius: 50%;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px; position: relative; z-index: 1;">
                            <div>
                                <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 5px;">MEDICAL COMPLIANCE</p>
                                <h3 style="font-size: 36px; font-weight: 800; color: #1f2937; margin: 0;">{{ $medicalStatus['valid_percentage'] }}%</h3>
                            </div>
                            <div style="background: linear-gradient(135deg, #ef444420, #dc262620); border-radius: 15px; padding: 10px;">
                                <i class="fas fa-notes-medical" style="color: #ef4444; font-size: 24px;"></i>
                            </div>
                        </div>
                        <div style="margin-top: 10px; position: relative; z-index: 1;">
                            <span style="color: #10b981; font-size: 13px; font-weight: 600;">✓ {{ $medicalStatus['valid'] }} Valid</span>
                            <span style="color: #f59e0b; font-size: 13px; font-weight: 600; margin-left: 10px;">⚠️ {{ $medicalStatus['expiring_soon'] }} Expiring Soon</span>
                        </div>
                    </div>
                </div>
                
                <!-- Row 3: Three Cards for Payroll, Advances & Hours -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <!-- MONTHLY PAYROLL CARD - Clickable to Payroll History -->
                    <div onclick="window.location='{{ route('payroll.history') }}'" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';">
                        <div style="position: absolute; top: -50%; right: -50%; width: 80px; height: 80px; background: radial-gradient(circle, rgba(139,92,246,0.05), transparent); border-radius: 50%;"></div>
                        <div style="position: relative; z-index: 1;">
                            <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 5px;">MONTHLY PAYROLL</p>
                            <h3 style="font-size: 24px; font-weight: 800; color: #1f2937; margin: 0;">€{{ number_format($quickStats['total_payroll_amount'], 2) }}</h3>
                            <small style="color: #6b7280;">{{ $quickStats['payroll_workers_count'] }} Workers</small>
                        </div>
                    </div>
                    
                    <!-- PENDING ADVANCES CARD - Clickable to Advances List -->
                    <div onclick="window.location='{{ route('hr.advances.list') }}'" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';">
                        <div style="position: absolute; top: -50%; right: -50%; width: 80px; height: 80px; background: radial-gradient(circle, rgba(239,68,68,0.05), transparent); border-radius: 50%;"></div>
                        <div style="position: relative; z-index: 1;">
                            <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 5px;">PENDING ADVANCES</p>
                            <h3 style="font-size: 24px; font-weight: 800; color: #1f2937; margin: 0;">€{{ number_format($quickStats['pending_advances_total'], 2) }}</h3>
                            <small style="color: #ef4444;">{{ $quickStats['workers_with_advances'] }} Workers</small>
                        </div>
                    </div>
                    
                    <!-- TOTAL HOURS WORKED CARD - Clickable to Attendance Sheet -->
                    <div onclick="window.location='{{ route('hr.attendancesheet') }}'" style="background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)';">
                        <div style="position: absolute; top: -50%; right: -50%; width: 80px; height: 80px; background: radial-gradient(circle, rgba(59,130,246,0.05), transparent); border-radius: 50%;"></div>
                        <div style="position: relative; z-index: 1;">
                            <p style="color: #6b7280; font-size: 12px; font-weight: 600; margin-bottom: 5px;">TOTAL HOURS WORKED</p>
                            <h3 style="font-size: 24px; font-weight: 800; color: #1f2937; margin: 0;">{{ number_format($attendanceStats['total_hours'], 1) }}</h3>
                            <small style="color: #f59e0b;">+ {{ number_format($attendanceStats['total_overtime'], 1) }} Overtime</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div style="background: white; border-radius: 15px; padding: 20px; margin: 30px 30px 30px 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 10px;">
            <button wire:click="$set('dateRange', 'current_month')" style="padding: 10px 24px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s; {{ $dateRange == 'current_month' ? 'background: linear-gradient(135deg, #ff0000 0%, #000000 100%); color: white; box-shadow: 0 5px 15px rgba(255,0,0,0.3);' : 'background: #f3f4f6; color: #4b5563;' }}">
                Current Month
            </button>
            <button wire:click="$set('dateRange', 'previous_month')" style="padding: 10px 24px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s; {{ $dateRange == 'previous_month' ? 'background: linear-gradient(135deg, #ff0000 0%, #000000 100%); color: white; box-shadow: 0 5px 15px rgba(255,0,0,0.3);' : 'background: #f3f4f6; color: #4b5563;' }}">
                Previous Month
            </button>
        </div>
        <div style="display: flex; gap: 10px;">
            <select wire:model="selectedMonth" style="padding: 10px 16px; border-radius: 10px; border: 1px solid #e5e7eb; background: white; cursor: pointer;">
                @foreach($months as $key => $month)
                    <option value="{{ $key }}">{{ $month }}</option>
                @endforeach
            </select>
            <select wire:model="selectedYear" style="padding: 10px 16px; border-radius: 10px; border: 1px solid #e5e7eb; background: white; cursor: pointer;">
                @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <button wire:click="loadDashboardData" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); color: white; padding: 10px 24px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 5px 15px rgba(255,0,0,0.3);">
                <i class="fas fa-sync-alt" style="margin-right: 8px;"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Quick Action Buttons Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin: 0 30px 30px 30px;">
        <button wire:click="goToPage('{{ route('hr.workers.list') }}')" class="quick-btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 15px; border: none; color: white; cursor: pointer; text-align: center; transition: all 0.3s; box-shadow: 0 10px 25px rgba(102,126,234,0.3);">
            <i class="fas fa-users" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            <div style="font-weight: 700; font-size: 16px;">Workers</div>
            <div style="font-size: 12px; opacity: 0.9; margin-top: 5px;">Manage & Edit</div>
        </button>
        
        <button wire:click="goToPage('{{ route('hr.attendance') }}')" class="quick-btn" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 20px; border-radius: 15px; border: none; color: white; cursor: pointer; text-align: center; transition: all 0.3s; box-shadow: 0 10px 25px rgba(240,147,251,0.3);">
            <i class="fas fa-clock" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            <div style="font-weight: 700; font-size: 16px;">Attendance</div>
            <div style="font-size: 12px; opacity: 0.9; margin-top: 5px;">Mark & View</div>
        </button>
        
        <button wire:click="goToPage('{{ route('hr.project.assignment') }}')" class="quick-btn" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 20px; border-radius: 15px; border: none; color: white; cursor: pointer; text-align: center; transition: all 0.3s; box-shadow: 0 10px 25px rgba(79,172,254,0.3);">
            <i class="fas fa-project-diagram" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            <div style="font-weight: 700; font-size: 16px;">Projects</div>
            <div style="font-size: 12px; opacity: 0.9; margin-top: 5px;">Assign Workers</div>
        </button>
        
        <button wire:click="goToPage('{{ route('payroll.history') }}')" class="quick-btn" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 20px; border-radius: 15px; border: none; color: white; cursor: pointer; text-align: center; transition: all 0.3s; box-shadow: 0 10px 25px rgba(250,112,154,0.3);">
            <i class="fas fa-file-invoice-dollar" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            <div style="font-weight: 700; font-size: 16px;">Payroll</div>
            <div style="font-size: 12px; opacity: 0.9; margin-top: 5px;">Generate & Manage</div>
        </button>
        
        <button wire:click="goToPage('{{ route('hr.advances.list') }}')" class="quick-btn" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); padding: 20px; border-radius: 15px; border: none; color: #333; cursor: pointer; text-align: center; transition: all 0.3s; box-shadow: 0 10px 25px rgba(168,237,234,0.3);">
            <i class="fas fa-hand-holding-usd" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            <div style="font-weight: 700; font-size: 16px;">Advances</div>
            <div style="font-size: 12px; opacity: 0.9; margin-top: 5px;">Track & Manage</div>
        </button>
        
        <button wire:click="goToPage('{{ route('hr.attendancesheet') }}')" class="quick-btn" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); padding: 20px; border-radius: 15px; border: none; color: #333; cursor: pointer; text-align: center; transition: all 0.3s; box-shadow: 0 10px 25px rgba(252,182,159,0.3);">
            <i class="fas fa-chart-line" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            <div style="font-weight: 700; font-size: 16px;">Reports</div>
            <div style="font-size: 12px; opacity: 0.9; margin-top: 5px;">Analytics & Data</div>
        </button>
        
        <button wire:click="goToPage('{{ route('hr.projects.list') }}')" class="quick-btn" style="background: linear-gradient(135deg, #b1f4cf 0%, #5e5bea 100%); padding: 20px; border-radius: 15px; border: none; color: white; cursor: pointer; text-align: center; transition: all 0.3s; box-shadow: 0 10px 25px rgba(94,91,234,0.3);">
            <i class="fas fa-building" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            <div style="font-weight: 700; font-size: 16px;">All Projects</div>
            <div style="font-size: 12px; opacity: 0.9; margin-top: 5px;">View & Edit</div>
        </button>
    </div>

    <!-- Stats Cards Row 1 - Clickable -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 0 30px 25px 30px;">
        
        <!-- Total Workers Card -->
        <div class="clickable-card" onclick="window.location='{{ route('hr.workers.list') }}'" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #6b7280; font-size: 13px; margin-bottom: 5px;">Total Workers</p>
                    <h3 style="font-size: 32px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ number_format($quickStats['total_workers']) }}</h3>
                    <small style="color: #10b981;">
                        <i class="fas fa-arrow-up"></i> {{ $quickStats['active_workers'] }} Active
                    </small>
                </div>
                <div style="background: rgba(255, 0, 0, 0.1); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-users fa-2x" style="color: #ff0000;"></i>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <div style="background: #e5e7eb; border-radius: 5px; height: 5px; overflow: hidden;">
                    <div class="progress-gradient" style="width: {{ ($quickStats['active_workers'] / max($quickStats['total_workers'], 1)) * 100 }}%; height: 100%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                    <small style="color: #6b7280;">Active: {{ $quickStats['active_workers'] }}</small>
                    <small style="color: #6b7280;">Inactive: {{ $quickStats['inactive_workers'] }}</small>
                </div>
            </div>
        </div>

        <!-- Active Projects Card -->
        <div class="clickable-card" onclick="window.location='{{ route('hr.projects.list') }}'" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #6b7280; font-size: 13px; margin-bottom: 5px;">Active Projects</p>
                    <h3 style="font-size: 32px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ number_format($quickStats['active_projects']) }}</h3>
                    <small style="color: #3b82f6;">
                        <i class="fas fa-check-circle"></i> {{ $quickStats['completed_projects'] }} Completed
                    </small>
                </div>
                <div style="background: rgba(16, 185, 129, 0.1); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-project-diagram fa-2x" style="color: #10b981;"></i>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <div style="background: #e5e7eb; border-radius: 5px; height: 5px; overflow: hidden;">
                    <div style="background: #10b981; width: {{ ($quickStats['completed_projects'] / max($quickStats['active_projects'] + $quickStats['completed_projects'], 1)) * 100 }}%; height: 100%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                    <small style="color: #6b7280;">Ongoing: {{ $quickStats['active_projects'] }}</small>
                    <small style="color: #6b7280;">Planning: {{ $quickStats['planning_projects'] }}</small>
                </div>
            </div>
        </div>

        <!-- Attendance Rate Card -->
        <div class="clickable-card" onclick="window.location='{{ route('hr.attendancesheet') }}'" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #6b7280; font-size: 13px; margin-bottom: 5px;">Attendance Rate</p>
                    <h3 style="font-size: 32px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ $quickStats['attendance_rate'] }}%</h3>
                    <small style="color: #6b7280;">
                        {{ $quickStats['attendance_days'] }} / {{ $quickStats['total_attendance_days'] }} days
                    </small>
                </div>
                <div style="background: rgba(245, 158, 11, 0.1); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-calendar-check fa-2x" style="color: #f59e0b;"></i>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <div style="background: #e5e7eb; border-radius: 5px; height: 5px; overflow: hidden;">
                    <div style="background: #f59e0b; width: {{ $quickStats['attendance_rate'] }}%; height: 100%;"></div>
                </div>
            </div>
        </div>

        <!-- Pending Advances Card -->
        <div class="clickable-card" onclick="window.location='{{ route('hr.advances.list') }}'" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #6b7280; font-size: 13px; margin-bottom: 5px;">Pending Advances</p>
                    <h3 style="font-size: 24px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">€{{ number_format($quickStats['pending_advances_total'], 2) }}</h3>
                    <small style="color: #ef4444;">
                        <i class="fas fa-users"></i> {{ $quickStats['workers_with_advances'] }} Workers
                    </small>
                </div>
                <div style="background: rgba(239, 68, 68, 0.1); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-hand-holding-usd fa-2x" style="color: #ef4444;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row 2 - Clickable -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 0 30px 30px 30px;">
        
        <!-- Monthly Payroll Card -->
        <div class="clickable-card" onclick="window.location='{{ route('payroll.history') }}'" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #6b7280; font-size: 13px; margin-bottom: 5px;">Monthly Payroll</p>
                    <h4 style="font-size: 22px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">€{{ number_format($quickStats['total_payroll_amount'], 2) }}</h4>
                    <small style="color: #6b7280;">{{ $quickStats['payroll_workers_count'] }} Workers Processed</small>
                </div>
                <div style="background: rgba(139, 92, 246, 0.1); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-coins fa-2x" style="color: #8b5cf6;"></i>
                </div>
            </div>
        </div>

        <!-- Total Hours Worked Card -->
        <div class="clickable-card" onclick="window.location='{{ route('hr.attendancesheet') }}'" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #6b7280; font-size: 13px; margin-bottom: 5px;">Total Hours Worked</p>
                    <h3 style="font-size: 32px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ number_format($attendanceStats['total_hours'], 1) }}</h3>
                    <small style="color: #f59e0b;">+ {{ number_format($attendanceStats['total_overtime'], 1) }} Overtime</small>
                </div>
                <div style="background: rgba(59, 130, 246, 0.1); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-clock fa-2x" style="color: #3b82f6;"></i>
                </div>
            </div>
        </div>

        <!-- Medical Compliance Card -->
        <div class="clickable-card" onclick="window.location='{{ route('hr.workers.list') }}'" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #6b7280; font-size: 13px; margin-bottom: 5px;">Medical Compliance</p>
                    <h3 style="font-size: 32px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ $medicalStatus['valid_percentage'] }}%</h3>
                    <small style="color: #10b981;">{{ $medicalStatus['valid'] }} / {{ $medicalStatus['total_workers'] }} Valid</small>
                </div>
                <div style="background: rgba(16, 185, 129, 0.1); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-notes-medical fa-2x" style="color: #10b981;"></i>
                </div>
            </div>
            <div style="margin-top: 10px;">
                <small style="color: #f59e0b;">
                    <i class="fas fa-exclamation-triangle"></i> {{ $medicalStatus['expiring_soon'] }} Expiring Soon
                </small>
            </div>
        </div>

        <!-- Today's Attendance Card -->
        <div class="clickable-card" onclick="window.location='{{ route('hr.attendance') }}'" style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #6b7280; font-size: 13px; margin-bottom: 5px;">Today's Attendance</p>
                    <h3 style="font-size: 32px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ $attendanceStats['today_present'] }}</h3>
                    <small style="color: #6b7280;">Present out of {{ $attendanceStats['today_present'] + $attendanceStats['today_half_day'] + $attendanceStats['today_absent'] }}</small>
                </div>
                <div style="background: rgba(16, 185, 129, 0.1); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user-check fa-2x" style="color: #10b981;"></i>
                </div>
            </div>
            <div style="margin-top: 10px; display: flex; gap: 8px;">
                <span style="background: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 12px; font-size: 11px;">Half: {{ $attendanceStats['today_half_day'] }}</span>
                <span style="background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 12px; font-size: 11px;">Absent: {{ $attendanceStats['today_absent'] }}</span>
            </div>
        </div>
    </div>

    <!-- Original Stats Cards Row 1 -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin: 0 30px 30px 30px;">
        
        <!-- Workers Card -->
        <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-users" style="color: white; font-size: 24px;"></i>
                </div>
                <div style="background: #10b98120; padding: 5px 12px; border-radius: 20px;">
                    <span style="color: #10b981; font-weight: 600; font-size: 12px;">{{ $workerStats['active_percentage'] }}% Active</span>
                </div>
            </div>
            <div style="font-size: 36px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ $workerStats['total'] }}</div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Total Workers</div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <div><span style="color: #10b981;">●</span> Active: {{ $workerStats['active'] }}</div>
                <div><span style="color: #6b7280;">●</span> Inactive: {{ $workerStats['inactive'] }}</div>
                <div><span style="color: #ef4444;">●</span> Terminated: {{ $workerStats['terminated'] }}</div>
            </div>
            <div style="border-top: 1px solid #e5e7eb; padding-top: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6b7280; font-size: 13px;">Joined this month</span>
                    <span style="font-weight: 700; color: #667eea;">{{ $workerStats['joined_this_month'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280; font-size: 13px;">Departments</span>
                    <span style="font-weight: 700; color: #667eea;">{{ $workerStats['department_count'] }}</span>
                </div>
            </div>
        </div>

        <!-- Attendance Card -->
        <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-calendar-check" style="color: white; font-size: 24px;"></i>
                </div>
                <div style="background: #f59e0b20; padding: 5px 12px; border-radius: 20px;">
                    <span style="color: #f59e0b; font-weight: 600; font-size: 12px;">{{ $attendanceStats['attendance_rate'] }}% Rate</span>
                </div>
            </div>
            <div style="font-size: 36px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ number_format($attendanceStats['total_hours'], 0) }}</div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Total Hours Worked</div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <div><span style="color: #10b981;">●</span> Present: {{ $attendanceStats['total_present'] }}</div>
                <div><span style="color: #f59e0b;">●</span> Half: {{ $attendanceStats['total_half_day'] }}</div>
                <div><span style="color: #ef4444;">●</span> Absent: {{ $attendanceStats['total_absent'] }}</div>
            </div>
            <div style="border-top: 1px solid #e5e7eb; padding-top: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6b7280; font-size: 13px;">Today's Attendance</span>
                    <span style="font-weight: 700;">P:{{ $attendanceStats['today_present'] }} H:{{ $attendanceStats['today_half_day'] }} A:{{ $attendanceStats['today_absent'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280; font-size: 13px;">Overtime Hours</span>
                    <span style="font-weight: 700; color: #f59e0b;">{{ number_format($attendanceStats['total_overtime'], 1) }} hrs</span>
                </div>
            </div>
        </div>

        <!-- Payroll Card -->
        <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-money-bill-wave" style="color: white; font-size: 24px;"></i>
                </div>
                <div style="background: {{ $payrollStats['gross_growth'] >= 0 ? '#10b98120' : '#ef444420' }}; padding: 5px 12px; border-radius: 20px;">
                    <span style="color: {{ $payrollStats['gross_growth'] >= 0 ? '#10b981' : '#ef4444' }}; font-weight: 600; font-size: 12px;">
                        {{ $payrollStats['gross_growth'] >= 0 ? '↑' : '↓' }} {{ abs($payrollStats['gross_growth']) }}%
                    </span>
                </div>
            </div>
            <div style="font-size: 28px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">€{{ number_format($payrollStats['total_net'], 2) }}</div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Net Payroll ({{ Carbon\Carbon::create()->month($selectedMonth)->format('F') }})</div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <div>Gross: €{{ number_format($payrollStats['total_gross'], 2) }}</div>
                <div style="color: #ef4444;">Ded: €{{ number_format($payrollStats['total_advance_deduction'], 2) }}</div>
            </div>
            <div style="border-top: 1px solid #e5e7eb; padding-top: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6b7280; font-size: 13px;">Workers Paid</span>
                    <span style="font-weight: 700;">{{ $payrollStats['total_workers_paid'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280; font-size: 13px;">YTD Net</span>
                    <span style="font-weight: 700;">€{{ number_format($payrollStats['ytd_net'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Projects Card -->
        <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chart-pie" style="color: white; font-size: 24px;"></i>
                </div>
                <div style="background: #8b5cf620; padding: 5px 12px; border-radius: 20px;">
                    <span style="color: #8b5cf6; font-weight: 600; font-size: 12px;">{{ $projectStats['active_percentage'] }}% Active</span>
                </div>
            </div>
            <div style="font-size: 36px; font-weight: 800; color: #1f2937; margin-bottom: 5px;">{{ $projectStats['active'] }}</div>
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">Active Projects</div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <div><span style="color: #3b82f6;">●</span> Plan: {{ $projectStats['planning'] }}</div>
                <div><span style="color: #f59e0b;">●</span> Hold: {{ $projectStats['on_hold'] }}</div>
                <div><span style="color: #10b981;">●</span> Done: {{ $projectStats['completed'] }}</div>
            </div>
            <div style="border-top: 1px solid #e5e7eb; padding-top: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6b7280; font-size: 13px;">Contract Value</span>
                    <span style="font-weight: 700;">€{{ number_format($projectStats['total_contract_value'], 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #6b7280; font-size: 13px;">Ending Soon</span>
                    <span style="font-weight: 700; color: #ef4444;">{{ $projectStats['ending_soon'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row 2 - Original -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin: 0 30px 30px 30px;">
        
        <!-- Advances Card -->
        <div class="dashboard-card" style="background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%); border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #c53030;">💰 Worker Advances</h3>
                <i class="fas fa-hand-holding-usd" style="font-size: 28px; color: #c53030;"></i>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
                <div>
                    <div style="font-size: 24px; font-weight: 800; color: #c53030;">€{{ number_format($advanceStats['pending_balance'], 2) }}</div>
                    <div style="font-size: 12px; color: #742a2a;">Pending Balance</div>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 800; color: #276749;">{{ $advanceStats['recovery_percentage'] }}%</div>
                    <div style="font-size: 12px; color: #742a2a;">Recovery Rate</div>
                </div>
                <div>
                    <div style="font-size: 18px; font-weight: 800; color: #c53030;">{{ $advanceStats['workers_with_advances'] }}</div>
                    <div style="font-size: 12px; color: #742a2a;">Workers with Advances</div>
                </div>
                <div>
                    <div style="font-size: 18px; font-weight: 800; color: #c53030;">€{{ number_format($advanceStats['average_advance'], 2) }}</div>
                    <div style="font-size: 12px; color: #742a2a;">Average Advance</div>
                </div>
            </div>
            <button wire:click="goToPage('{{ route('hr.advances.list') }}')" style="width: 100%; background: linear-gradient(135deg, #c53030 0%, #9b2c2c 100%); color: white; padding: 12px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                Manage Advances <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>

        <!-- Medical Compliance Card -->
        <div class="dashboard-card" style="background: linear-gradient(135deg, #e6fffa 0%, #b2f5ea 100%); border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #234e52;">🏥 Medical Compliance</h3>
                <i class="fas fa-notes-medical" style="font-size: 28px; color: #234e52;"></i>
            </div>
            <div style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Valid Certificates</span>
                    <span style="font-weight: 700;">{{ $workerStats['valid_medical'] }}/{{ $workerStats['total'] }}</span>
                </div>
                <div style="background: #81e6d9; border-radius: 10px; height: 8px; overflow: hidden;">
                    <div style="background: linear-gradient(135deg, #38b2ac 0%, #319795 100%); width: {{ $workerStats['medical_percentage'] }}%; height: 100%; border-radius: 10px;"></div>
                </div>
            </div>
            @if(count($medicalExpiringWorkers) > 0)
                <div style="background: rgba(255,255,255,0.7); border-radius: 12px; padding: 12px;">
                    <p style="font-size: 12px; font-weight: 700; color: #234e52; margin-bottom: 8px;">⚠️ Expiring Soon ({{ count($medicalExpiringWorkers) }})</p>
                    @foreach($medicalExpiringWorkers->take(2) as $worker)
                        <p style="font-size: 12px; color: #285e61; margin-bottom: 4px;">{{ $worker['name'] }} - {{ $worker['days_left'] }} days left</p>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Alerts Card -->
        <div class="dashboard-card" style="background: linear-gradient(135deg, #fffff0 0%, #fefcbf 100%); border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #744210;">⚠️ High Advance Balances</h3>
                <i class="fas fa-exclamation-triangle" style="font-size: 28px; color: #744210;"></i>
            </div>
            @if(count($workersWithHighAdvances) > 0)
                <div style="max-height: 150px; overflow-y: auto;">
                    @foreach($workersWithHighAdvances as $worker)
                        <div style="background: rgba(255,255,255,0.6); border-radius: 10px; padding: 10px; margin-bottom: 8px;">
                            <p style="font-weight: 700; color: #744210; font-size: 14px;">{{ $worker['name'] }}</p>
                            <p style="font-size: 12px; color: #975a16;">Pending: €{{ number_format($worker['pending_balance'], 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: #744210; text-align: center; padding: 20px;">No high advance balances</p>
            @endif
        </div>
    </div>

    <!-- REPLACED CHARTS SECTION WITH TABLES -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 25px; margin: 0 30px 30px 30px;">
        
        <!-- Attendance Trend Table -->
        <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1f2937;">
                    <i class="fas fa-chart-line" style="margin-right: 10px; color: #ff0000;"></i>Daily Attendance Trend
                </h3>
                <span class="badge" style="background: #ff000010; padding: 5px 12px; border-radius: 20px; font-size: 12px; color: #ff0000;">
                    {{ count($attendanceTrend) }} Days Recorded
                </span>
            </div>
            <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 12px; text-align: left; font-weight: 600; color: #4b5563;">Date</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #4b5563;">Present</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #4b5563;">Half Day</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #4b5563;">Absent</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #4b5563;">Hours</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; color: #4b5563;">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceTrend as $trend)
                            <tr class="table-row" style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 12px; font-weight: 500; color: #1f2937;">{{ $trend['date'] }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background: #10b98120; color: #10b981; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ $trend['present'] }}</span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background: #f59e0b20; color: #f59e0b; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ $trend['half_day'] }}</span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="background: #ef444420; color: #ef4444; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ $trend['absent'] }}</span>
                                </td>
                                <td style="padding: 12px; text-align: center; font-weight: 500;">{{ number_format($trend['hours'], 1) }} hrs</td>
                                <td style="padding: 12px; text-align: center;">
                                    @php
                                        $total = $trend['present'] + $trend['half_day'] + $trend['absent'];
                                        $percentage = $total > 0 ? round((($trend['present'] + ($trend['half_day'] * 0.5)) / $total) * 100, 1) : 0;
                                    @endphp
                                    <div style="display: inline-block; background: {{ $percentage >= 80 ? '#10b98120' : ($percentage >= 60 ? '#f59e0b20' : '#ef444420') }}; padding: 4px 10px; border-radius: 20px;">
                                        <span style="color: {{ $percentage >= 80 ? '#10b981' : ($percentage >= 60 ? '#f59e0b' : '#ef4444') }}; font-weight: 600; font-size: 12px;">{{ $percentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 40px; text-align: center; color: #6b7280;">
                                    <i class="fas fa-chart-line" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                    No attendance data available for this period
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Weekly Performance & Department Distribution Combined -->
        <div style="display: flex; flex-direction: column; gap: 25px;">
            
            <!-- Weekly Performance Card -->
            <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #1f2937;">
                        <i class="fas fa-chart-bar" style="margin-right: 10px; color: #ff0000;"></i>Weekly Performance
                    </h3>
                </div>
                <div>
                    @forelse($weeklyAttendance as $week)
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 600; color: #4b5563;">{{ $week['week'] }}</span>
                                <span style="color: #6b7280; font-size: 13px;">{{ $week['present'] }} days | {{ $week['hours'] }} hrs</span>
                            </div>
                            <div style="background: #e5e7eb; border-radius: 8px; height: 10px; overflow: hidden;">
                                <div style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); width: {{ min(100, ($week['present'] / 7) * 100) }}%; height: 100%; transition: width 0.5s ease;"></div>
                            </div>
                            <div style="margin-top: 5px; font-size: 11px; color: #6b7280;">
                                Attendance Rate: {{ round(($week['present'] / 7) * 100, 1) }}%
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; padding: 40px; color: #6b7280;">
                            <i class="fas fa-chart-bar" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            No weekly data available
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Department Distribution Card -->
            <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #1f2937;">
                        <i class="fas fa-building" style="margin-right: 10px; color: #ff0000;"></i>Department Distribution
                    </h3>
                </div>
                <div>
                    @forelse($departmentDistribution as $dept)
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 600; color: #4b5563;">{{ $dept['department'] }}</span>
                                <span style="color: #6b7280; font-size: 13px;">{{ $dept['count'] }} workers ({{ round(($dept['count'] / max($workerStats['active'], 1)) * 100, 1) }}%)</span>
                            </div>
                            <div style="background: #e5e7eb; border-radius: 8px; height: 10px; overflow: hidden;">
                                <div style="background: linear-gradient(135deg, #ff0000 0%, #8b0000 100%); width: {{ ($dept['count'] / max($workerStats['active'], 1)) * 100 }}%; height: 100%; transition: width 0.5s ease;"></div>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; padding: 40px; color: #6b7280;">
                            <i class="fas fa-building" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                            No department data available
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; margin: 0 30px 30px 30px;">
        
        <!-- Top Performers -->
        <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1f2937;">🏆 Top Performers (This Month)</h3>
                <i class="fas fa-trophy" style="font-size: 24px; color: #f59e0b;"></i>
            </div>
            <div>
                @forelse($topPerformers as $index => $performer)
                    <div style="display: flex; align-items: center; gap: 15px; padding: 12px; background: {{ $index == 0 ? 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)' : '#f9fafb' }}; border-radius: 12px; margin-bottom: 10px;">
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800;">
                            {{ $index + 1 }}
                        </div>
                        <div style="flex: 1;">
                            <p style="font-weight: 700; color: #1f2937;">{{ $performer['name'] }}</p>
                            <p style="font-size: 12px; color: #6b7280;">{{ $performer['designation'] }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-weight: 700; color: #10b981;">{{ $performer['present_days'] }} days</p>
                            <p style="font-size: 12px; color: #6b7280;">{{ $performer['total_hours'] }} hrs</p>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; padding: 40px; color: #6b7280;">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity & Deadlines -->
        <div class="dashboard-card" style="background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1f2937;">🔄 Recent Activity</h3>
                <i class="fas fa-history" style="font-size: 20px; color: #6b7280;"></i>
            </div>
            <div style="margin-bottom: 25px; max-height: 250px; overflow-y: auto;">
                @forelse($recentActivities as $activity)
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border-bottom: 1px solid #e5e7eb;">
                        <div style="width: 35px; height: 35px; background: {{ $activity['type'] == 'worker' ? '#10b98120' : ($activity['type'] == 'attendance' ? '#3b82f620' : '#8b5cf620') }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas {{ $activity['type'] == 'worker' ? 'fa-user-plus' : ($activity['type'] == 'attendance' ? 'fa-clock' : 'fa-file-invoice') }}" style="color: {{ $activity['type'] == 'worker' ? '#10b981' : ($activity['type'] == 'attendance' ? '#3b82f6' : '#8b5cf6') }};"></i>
                        </div>
                        <div style="flex: 1;">
                            <p style="font-weight: 600; color: #1f2937; font-size: 14px;">{{ $activity['title'] }}</p>
                            <p style="font-size: 12px; color: #6b7280;">{{ $activity['description'] }}</p>
                        </div>
                        <div style="font-size: 11px; color: #9ca3af;">{{ $activity['time'] }}</div>
                    </div>
                @empty
                    <p style="text-align: center; padding: 20px; color: #6b7280;">No recent activity</p>
                @endforelse
            </div>
            
            <div style="border-top: 2px solid #e5e7eb; padding-top: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 15px;">📅 Upcoming Deadlines</h3>
                @forelse($upcomingDeadlines as $deadline)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #fef3c7; border-radius: 10px; margin-bottom: 8px;">
                        <div>
                            <p style="font-weight: 600; font-size: 13px;">{{ $deadline['title'] }}</p>
                            <p style="font-size: 11px; color: #92400e;">{{ $deadline['type'] }} Certificate</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-weight: 700; color: #dc2626; font-size: 14px;">{{ $deadline['days_left'] }} days</p>
                            <p style="font-size: 10px; color: #92400e;">left</p>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; padding: 20px; color: #6b7280;">No upcoming deadlines</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- CLOSE THE MAIN CARD WRAPPER -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function() {
            // Update time every minute
            setInterval(function() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                const timeElement = document.getElementById('currentTime');
                if (timeElement) timeElement.textContent = timeString;
            }, 60000);
        });
    </script>
</div>