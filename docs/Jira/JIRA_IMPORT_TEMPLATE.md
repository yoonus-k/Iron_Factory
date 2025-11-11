# 📊 Jira Import Template - Factory Production System

## Quick Import Format for Jira

This document provides CSV-ready tables that can be directly imported into Jira.

---

## 🎯 COMPONENTS TABLE

| Component ID | Name | Description | Lead | Default Assignee |
|-------------|------|-------------|------|------------------|
| COMP-01 | Backend API | Server-side API development with Laravel | Backend Lead | Backend Team |
| COMP-02 | Frontend UI | User interface development | Frontend Lead | Frontend Team |
| COMP-03 | Database | Database design and implementation | Database Lead | Backend Team |
| COMP-04 | Barcode System | Barcode generation, scanning, printing | Full Stack Lead | Full Stack Team |
| COMP-05 | Inventory Management | Stock tracking across all stages | Backend Lead | Backend Team |
| COMP-06 | Authentication | User management and permissions | Backend Lead | Backend Team |
| COMP-07 | Reporting | Reports, charts, and analytics | Full Stack Lead | Full Stack Team |
| COMP-08 | Offline Features | PWA and offline functionality | Frontend Lead | Frontend Team |
| COMP-09 | Testing QA | Testing, bug fixing, quality assurance | QA Lead | QA Team |
| COMP-10 | DevOps | Server setup, deployment, CI/CD | DevOps Lead | DevOps Team |
| COMP-11 | Documentation | User guides, technical docs, training | Tech Writer | All Teams |

---

## 📦 EPICS TABLE (All 60+ Epics)

| Epic Key | Epic Name | Component | Summary | Story Points | Priority | Sprint | Status |
|----------|-----------|-----------|---------|--------------|----------|--------|--------|
| FPMS-1 | API Architecture Setup | Backend API | Setup Laravel project, routing, middleware | 13 | Critical | Sprint 1 | To Do |
| FPMS-2 | Warehouse Management API | Backend API | APIs for raw materials management | 21 | Critical | Sprint 2 | To Do |
| FPMS-3 | Production Stages API | Backend API | APIs for all 4 production stages | 34 | Critical | Sprint 3 | To Do |
| FPMS-4 | Tracking & History API | Backend API | Product tracking and history endpoints | 21 | High | Sprint 3 | To Do |
| FPMS-5 | Reporting API | Backend API | Report generation endpoints | 21 | High | Sprint 4 | To Do |
| FPMS-6 | Data Validation Logic | Backend API | Validation rules, weight calculations | 13 | Critical | Sprint 2 | To Do |
| FPMS-7 | UI Framework Setup | Frontend UI | Setup React/Vue, routing, state management | 13 | Critical | Sprint 1 | To Do |
| FPMS-8 | Dashboard Statistics | Frontend UI | Main dashboard with charts and KPIs | 21 | Critical | Sprint 3 | To Do |
| FPMS-9 | Warehouse Interface | Frontend UI | UI for adding/managing raw materials | 21 | Critical | Sprint 2 | To Do |
| FPMS-10 | Production Stages UI | Frontend UI | UI for all 4 production stages | 34 | Critical | Sprint 2-3 | To Do |
| FPMS-11 | Tracking Interface | Frontend UI | Product tracking visualization | 13 | High | Sprint 3 | To Do |
| FPMS-12 | Reports Interface | Frontend UI | Reports viewing and generation UI | 21 | High | Sprint 4 | To Do |
| FPMS-13 | Responsive Design | Frontend UI | Mobile and tablet optimization | 13 | Medium | Sprint 3 | To Do |
| FPMS-14 | Database Schema Design | Database | ER diagram, table structures | 8 | Critical | Sprint 1 | To Do |
| FPMS-15 | Database Implementation | Database | Create tables, relationships, indexes | 13 | Critical | Sprint 1 | To Do |
| FPMS-16 | Database Migrations | Database | Laravel migrations for all tables | 8 | Critical | Sprint 1 | To Do |
| FPMS-17 | Seeders Test Data | Database | Create seeders for development | 5 | Medium | Sprint 1 | To Do |
| FPMS-18 | Database Optimization | Database | Indexes, query optimization | 8 | High | Sprint 3 | To Do |
| FPMS-19 | Barcode Generation | Barcode System | Generate unique barcodes for all stages | 13 | Critical | Sprint 2 | To Do |
| FPMS-20 | Barcode Scanning | Barcode System | Camera-based barcode scanning | 21 | High | Sprint 3 | To Do |
| FPMS-21 | Barcode Printing | Barcode System | Print barcode labels with QR codes | 13 | High | Sprint 3 | To Do |
| FPMS-22 | Barcode Validation | Barcode System | Validate and parse barcode formats | 8 | High | Sprint 2 | To Do |
| FPMS-23 | Material Tracking | Inventory | Track materials across stages | 21 | Critical | Sprint 2 | To Do |
| FPMS-24 | Weight Management | Inventory | Calculate remaining weights, waste | 13 | Critical | Sprint 2 | To Do |
| FPMS-25 | Stage Transitions | Inventory | Handle product transitions between stages | 21 | Critical | Sprint 3 | To Do |
| FPMS-26 | Inventory Alerts | Inventory | Low stock, high waste alerts | 13 | High | Sprint 3 | To Do |
| FPMS-27 | User Authentication | Authentication | Login, logout, JWT tokens | 13 | Critical | Sprint 1 | To Do |
| FPMS-28 | Role Based Access | Authentication | 4 roles with different permissions | 13 | Critical | Sprint 1 | To Do |
| FPMS-29 | User Management | Authentication | CRUD operations for users | 13 | High | Sprint 4 | To Do |
| FPMS-30 | Password Management | Authentication | Reset, change password | 8 | Medium | Sprint 4 | To Do |
| FPMS-31 | Dashboard Analytics | Reporting | Real-time statistics and KPIs | 21 | Critical | Sprint 3 | To Do |
| FPMS-32 | Production Reports | Reporting | Daily, weekly, monthly reports | 21 | High | Sprint 4 | To Do |
| FPMS-33 | Waste Analysis | Reporting | Waste reports and analytics | 13 | High | Sprint 4 | To Do |
| FPMS-34 | Export Functionality | Reporting | PDF, Excel export | 13 | Medium | Sprint 4 | To Do |
| FPMS-35 | Charts Visualizations | Reporting | Chart.js integration | 13 | High | Sprint 3 | To Do |
| FPMS-36 | PWA Setup | Offline Features | Service worker, manifest | 13 | Medium | Post-Launch | To Do |
| FPMS-37 | Offline Storage | Offline Features | IndexedDB for offline data | 13 | Medium | Post-Launch | To Do |
| FPMS-38 | Data Synchronization | Offline Features | Sync when online | 21 | Medium | Post-Launch | To Do |
| FPMS-39 | Offline UI Indicators | Offline Features | Show online/offline status | 5 | Low | Post-Launch | To Do |
| FPMS-40 | Unit Testing | Testing QA | Backend unit tests | 21 | High | Sprint 4 | To Do |
| FPMS-41 | Integration Testing | Testing QA | API integration tests | 21 | High | Sprint 4 | To Do |
| FPMS-42 | Frontend Testing | Testing QA | Component and E2E tests | 21 | High | Sprint 4 | To Do |
| FPMS-43 | UAT Testing | Testing QA | UAT with client | 13 | High | Sprint 4 | To Do |
| FPMS-44 | Bug Fixing | Testing QA | Fix identified bugs | 21 | High | Sprint 4 | To Do |
| FPMS-45 | Server Setup | DevOps | Configure production server | 13 | High | Sprint 4 | To Do |
| FPMS-46 | CI/CD Pipeline | DevOps | Automated deployment | 13 | Medium | Sprint 4 | To Do |
| FPMS-47 | Database Migration Prod | DevOps | Production database setup | 8 | Critical | Sprint 4 | To Do |
| FPMS-48 | SSL Security | DevOps | HTTPS, security headers | 8 | Critical | Sprint 4 | To Do |
| FPMS-49 | Backup Monitoring | DevOps | Automated backups, monitoring | 13 | High | Sprint 4 | To Do |
| FPMS-50 | API Documentation | Documentation | Swagger/OpenAPI docs | 8 | Medium | Sprint 3 | To Do |
| FPMS-51 | User Guide | Documentation | End-user documentation | 13 | High | Sprint 4 | To Do |
| FPMS-52 | Technical Docs | Documentation | Developer docs, architecture | 13 | Medium | Sprint 4 | To Do |
| FPMS-53 | Video Tutorials | Documentation | Training videos | 8 | Low | Post-Launch | To Do |
| FPMS-54 | On-site Training | Documentation | Train factory staff | 13 | High | Sprint 4 | To Do |

---

## 📝 USER STORIES TABLE (Sample - Top 50 Critical Stories)

| Story Key | Story Name | Epic | Description | Story Points | Priority | Sprint | Assignee |
|-----------|------------|------|-------------|--------------|----------|--------|----------|
| FPMS-101 | Setup Laravel Project | FPMS-1 | As a developer, I want to setup Laravel project structure | 3 | Critical | Sprint 1 | Backend Dev |
| FPMS-102 | Configure API Routing | FPMS-1 | As a developer, I want to configure API routing | 3 | Critical | Sprint 1 | Backend Dev |
| FPMS-103 | Setup Middleware | FPMS-1 | As a developer, I want to setup middleware | 3 | Critical | Sprint 1 | Backend Dev |
| FPMS-104 | Configure Error Handling | FPMS-1 | As a developer, I want to configure error handling | 2 | High | Sprint 1 | Backend Dev |
| FPMS-105 | API Response Structure | FPMS-1 | As a developer, I want to setup API response structure | 2 | High | Sprint 1 | Backend Dev |
| FPMS-106 | Add Raw Material API | FPMS-2 | As a warehouse manager, I want to add raw materials via API | 5 | Critical | Sprint 2 | Backend Dev |
| FPMS-107 | View All Materials API | FPMS-2 | As a warehouse manager, I want to view all materials | 3 | Critical | Sprint 2 | Backend Dev |
| FPMS-108 | View Single Material API | FPMS-2 | As a warehouse manager, I want to view single material | 2 | Critical | Sprint 2 | Backend Dev |
| FPMS-109 | Update Material API | FPMS-2 | As a warehouse manager, I want to update material info | 3 | High | Sprint 2 | Backend Dev |
| FPMS-110 | Search Materials API | FPMS-2 | As a warehouse manager, I want to search materials | 3 | Medium | Sprint 2 | Backend Dev |
| FPMS-111 | Auto-Update Weight | FPMS-2 | As a developer, I want material weight to auto-update | 5 | Critical | Sprint 2 | Backend Dev |
| FPMS-112 | Create Stand API | FPMS-3 | As a stage1 worker, I want to create stands via API | 5 | Critical | Sprint 3 | Backend Dev |
| FPMS-113 | View All Stands API | FPMS-3 | As a stage1 worker, I want to view all stands | 3 | Critical | Sprint 3 | Backend Dev |
| FPMS-114 | Process Stand Stage2 | FPMS-3 | As a stage2 worker, I want to process stands | 5 | Critical | Sprint 3 | Backend Dev |
| FPMS-115 | Create Coil API | FPMS-3 | As a stage3 worker, I want to create coils | 5 | Critical | Sprint 3 | Backend Dev |
| FPMS-116 | View All Coils API | FPMS-3 | As a stage3 worker, I want to view all coils | 3 | Critical | Sprint 3 | Backend Dev |
| FPMS-117 | Create Box API | FPMS-3 | As a stage4 worker, I want to create boxes | 5 | Critical | Sprint 3 | Backend Dev |
| FPMS-118 | Mark Box Shipped | FPMS-3 | As a stage4 worker, I want to mark box as shipped | 3 | High | Sprint 3 | Backend Dev |
| FPMS-119 | Stage Transition Validation | FPMS-3 | As a developer, I want validation for stage transitions | 5 | Critical | Sprint 3 | Backend Dev |
| FPMS-120 | Track by Barcode API | FPMS-4 | As a manager, I want to track product by barcode | 8 | High | Sprint 3 | Backend Dev |
| FPMS-121 | View Product History | FPMS-4 | As a manager, I want to view product history | 5 | High | Sprint 3 | Backend Dev |
| FPMS-122 | Search Products API | FPMS-4 | As a manager, I want to search products | 5 | Medium | Sprint 3 | Backend Dev |
| FPMS-123 | Log All Operations | FPMS-4 | As a developer, I want to log all operations | 3 | Medium | Sprint 3 | Backend Dev |
| FPMS-124 | Dashboard KPIs | FPMS-8 | As a manager, I want to see dashboard KPIs | 5 | Critical | Sprint 3 | Frontend Dev |
| FPMS-125 | Production Chart | FPMS-8 | As a manager, I want to see production chart | 5 | Critical | Sprint 3 | Frontend Dev |
| FPMS-126 | Waste Distribution Chart | FPMS-8 | As a manager, I want to see waste distribution | 3 | High | Sprint 3 | Frontend Dev |
| FPMS-127 | Recent Activities Widget | FPMS-8 | As a manager, I want to see recent activities | 3 | High | Sprint 3 | Frontend Dev |
| FPMS-128 | Dashboard Auto-Refresh | FPMS-8 | As a manager, I want dashboard to auto-refresh | 5 | Medium | Sprint 3 | Frontend Dev |
| FPMS-129 | Add Material Form | FPMS-9 | As a warehouse manager, I want a form to add materials | 5 | Critical | Sprint 2 | Frontend Dev |
| FPMS-130 | Materials Table View | FPMS-9 | As a warehouse manager, I want to view materials table | 5 | Critical | Sprint 2 | Frontend Dev |
| FPMS-131 | Barcode Modal | FPMS-9 | As a warehouse manager, I want to view barcode modal | 3 | High | Sprint 2 | Frontend Dev |
| FPMS-132 | Filter Materials | FPMS-9 | As a warehouse manager, I want to filter materials | 3 | Medium | Sprint 2 | Frontend Dev |
| FPMS-133 | Export Materials List | FPMS-9 | As a warehouse manager, I want to export materials list | 5 | Medium | Sprint 3 | Frontend Dev |
| FPMS-134 | Stage1 Scan Barcode | FPMS-10 | As a stage1 worker, I want to scan material barcode | 5 | Critical | Sprint 2 | Frontend Dev |
| FPMS-135 | Stage1 Add Stand Form | FPMS-10 | As a stage1 worker, I want to add stand form | 5 | Critical | Sprint 2 | Frontend Dev |
| FPMS-136 | Stage1 Stands List | FPMS-10 | As a stage1 worker, I want to view stands list | 3 | Critical | Sprint 2 | Frontend Dev |
| FPMS-137 | Stage2 Process Form | FPMS-10 | As a stage2 worker, I want to process stand form | 5 | Critical | Sprint 3 | Frontend Dev |
| FPMS-138 | Stage3 Create Coil Form | FPMS-10 | As a stage3 worker, I want to create coil form | 5 | Critical | Sprint 3 | Frontend Dev |
| FPMS-139 | Stage3 Coils Grid | FPMS-10 | As a stage3 worker, I want to view coils grid | 3 | Critical | Sprint 3 | Frontend Dev |
| FPMS-140 | Stage4 Create Box Form | FPMS-10 | As a stage4 worker, I want to create box form | 5 | Critical | Sprint 3 | Frontend Dev |
| FPMS-141 | Stage4 Boxes Grid | FPMS-10 | As a stage4 worker, I want to view boxes grid | 3 | Critical | Sprint 3 | Frontend Dev |
| FPMS-142 | Tracking Search | FPMS-11 | As a user, I want to search by barcode for tracking | 5 | High | Sprint 3 | Frontend Dev |
| FPMS-143 | Tracking Chain View | FPMS-11 | As a user, I want to view full tracking chain | 5 | High | Sprint 3 | Frontend Dev |
| FPMS-144 | Tracking Timeline | FPMS-11 | As a user, I want to see timeline visualization | 3 | Medium | Sprint 3 | Frontend Dev |
| FPMS-145 | Generate Unique Barcode | FPMS-19 | As a system, I want to generate unique barcodes | 5 | Critical | Sprint 2 | Backend Dev |
| FPMS-146 | Barcode Format Rules | FPMS-19 | As a developer, I want barcode format per stage | 3 | Critical | Sprint 2 | Backend Dev |
| FPMS-147 | Prevent Duplicate Barcode | FPMS-19 | As a system, I want to prevent duplicate barcodes | 3 | Critical | Sprint 2 | Backend Dev |
| FPMS-148 | Parse Barcode | FPMS-22 | As a developer, I want to parse barcodes | 2 | High | Sprint 2 | Backend Dev |
| FPMS-149 | Camera Barcode Scan | FPMS-20 | As a worker, I want to scan barcode with camera | 8 | High | Sprint 3 | Full Stack Dev |
| FPMS-150 | Manual Barcode Entry | FPMS-20 | As a worker, I want to manually enter barcode | 3 | High | Sprint 3 | Frontend Dev |

---

## 🔧 SUBTASKS TABLE (Sample - Top 30 Subtasks)

| Subtask Key | Subtask Name | Parent Story | Description | Estimated Hours | Assignee | Status |
|-------------|--------------|--------------|-------------|-----------------|----------|--------|
| FPMS-1001 | Create Material Model | FPMS-106 | Create Material model with schema | 2h | Backend Dev | To Do |
| FPMS-1002 | Create MaterialController | FPMS-106 | Create MaterialController with store method | 2h | Backend Dev | To Do |
| FPMS-1003 | Implement Validation | FPMS-106 | Implement validation rules (FormRequest) | 2h | Backend Dev | To Do |
| FPMS-1004 | Integrate Barcode Gen | FPMS-106 | Integrate barcode generation on creation | 3h | Backend Dev | To Do |
| FPMS-1005 | Write API Tests | FPMS-106 | Write API tests for material creation | 2h | QA Dev | To Do |
| FPMS-1006 | Document Endpoint | FPMS-106 | Document API endpoint in Swagger | 1h | Backend Dev | To Do |
| FPMS-1007 | Design Form Component | FPMS-129 | Design form component (Figma/Sketch) | 2h | UI Designer | To Do |
| FPMS-1008 | Create React Component | FPMS-129 | Create React/Vue form component | 3h | Frontend Dev | To Do |
| FPMS-1009 | Form Validation | FPMS-129 | Implement form validation | 2h | Frontend Dev | To Do |
| FPMS-1010 | Connect API | FPMS-129 | Connect to API endpoint | 2h | Frontend Dev | To Do |
| FPMS-1011 | Add Notifications | FPMS-129 | Add success/error notifications | 1h | Frontend Dev | To Do |
| FPMS-1012 | Write Component Tests | FPMS-129 | Write component tests | 2h | QA Dev | To Do |
| FPMS-1013 | Research Scanner Libs | FPMS-149 | Research barcode scanning libraries | 2h | Full Stack Dev | To Do |
| FPMS-1014 | Camera Permissions | FPMS-149 | Implement camera access permissions | 2h | Frontend Dev | To Do |
| FPMS-1015 | Scanner Component | FPMS-149 | Create barcode scanner component | 4h | Frontend Dev | To Do |
| FPMS-1016 | Detection Algorithm | FPMS-149 | Add barcode detection algorithm | 3h | Frontend Dev | To Do |
| FPMS-1017 | Beep Sound | FPMS-149 | Implement beep sound on successful scan | 1h | Frontend Dev | To Do |
| FPMS-1018 | Error Handling Camera | FPMS-149 | Add error handling for camera issues | 2h | Frontend Dev | To Do |
| FPMS-1019 | Test Multiple Devices | FPMS-149 | Test on multiple devices | 3h | QA Dev | To Do |
| FPMS-1020 | Create Stand Model | FPMS-112 | Create Stand model with relationships | 2h | Backend Dev | To Do |
| FPMS-1021 | StandController Store | FPMS-112 | Create StandController store method | 3h | Backend Dev | To Do |
| FPMS-1022 | Weight Validation | FPMS-112 | Validate weight against parent material | 2h | Backend Dev | To Do |
| FPMS-1023 | Barcode Generation ST1 | FPMS-112 | Generate ST1 barcode on creation | 2h | Backend Dev | To Do |
| FPMS-1024 | Update Parent Weight | FPMS-112 | Update parent material remaining weight | 2h | Backend Dev | To Do |
| FPMS-1025 | Write Stand Tests | FPMS-112 | Write API tests for stand creation | 2h | QA Dev | To Do |
| FPMS-1026 | Design Report Template | FPMS-32 | Design report templates | 3h | UI Designer | To Do |
| FPMS-1027 | ReportController | FPMS-32 | Create ReportController with endpoints | 3h | Backend Dev | To Do |
| FPMS-1028 | Daily Report Logic | FPMS-32 | Implement daily report logic | 4h | Backend Dev | To Do |
| FPMS-1029 | Weekly Report Logic | FPMS-32 | Implement weekly report logic | 3h | Backend Dev | To Do |
| FPMS-1030 | Monthly Report Logic | FPMS-32 | Implement monthly report logic | 3h | Backend Dev | To Do |

---

## 📊 SUMMARY STATISTICS

### By Component:

| Component | Epics | Stories | Subtasks | Total Points | Priority Level |
|-----------|-------|---------|----------|--------------|----------------|
| Backend API | 6 | 50+ | 150+ | 123 | Critical |
| Frontend UI | 7 | 60+ | 180+ | 136 | Critical |
| Database | 5 | 15 | 45 | 42 | Critical |
| Barcode System | 4 | 20 | 60 | 55 | High |
| Inventory | 4 | 25 | 75 | 68 | Critical |
| Authentication | 4 | 18 | 54 | 47 | Critical |
| Reporting | 5 | 30 | 90 | 81 | High |
| Offline Features | 4 | 15 | 45 | 52 | Medium |
| Testing QA | 5 | 40 | 120 | 97 | High |
| DevOps | 5 | 20 | 60 | 55 | High |
| Documentation | 5 | 18 | 54 | 55 | Medium |

### By Sprint:

| Sprint | Duration | Epics | Stories | Points | Team Capacity |
|--------|----------|-------|---------|--------|---------------|
| Sprint 1 | Week 1-2 | 5 | 25 | 60 | 116 points |
| Sprint 2 | Week 3-4 | 6 | 35 | 76 | 116 points |
| Sprint 3 | Week 5-6 | 8 | 45 | 110 | 116 points |
| Sprint 4 | Week 7-8 | 10 | 50 | 123 | 116 points |

### By Priority:

| Priority | Epics | Stories | Percentage |
|----------|-------|---------|------------|
| Critical | 28 | 120+ | 52% |
| High | 18 | 80+ | 33% |
| Medium | 6 | 30+ | 11% |
| Low | 2 | 10+ | 4% |

---

## 📋 CSV EXPORT FORMAT

### For Epics:
```csv
Epic Key,Epic Name,Component,Summary,Story Points,Priority,Sprint,Status
FPMS-1,API Architecture Setup,Backend API,Setup Laravel project structure,13,Critical,Sprint 1,To Do
FPMS-2,Warehouse Management API,Backend API,APIs for raw materials,21,Critical,Sprint 2,To Do
```

### For Stories:
```csv
Story Key,Story Name,Epic,Description,Story Points,Priority,Sprint,Assignee,Status
FPMS-101,Setup Laravel Project,FPMS-1,Setup Laravel project,3,Critical,Sprint 1,Backend Dev,To Do
FPMS-106,Add Raw Material API,FPMS-2,Add raw materials via API,5,Critical,Sprint 2,Backend Dev,To Do
```

### For Subtasks:
```csv
Subtask Key,Subtask Name,Parent Story,Description,Estimated Hours,Assignee,Status
FPMS-1001,Create Material Model,FPMS-106,Create Material model,2h,Backend Dev,To Do
FPMS-1002,Create MaterialController,FPMS-106,Create controller,2h,Backend Dev,To Do
```

---

## 🚀 IMPORT INSTRUCTIONS

### Step 1: Prepare Jira Project
1. Create new Jira project (Key: FPMS)
2. Select "Scrum" template
3. Add team members

### Step 2: Create Custom Fields
- Story Points (Number)
- Component (Single Select)
- Sprint (Select List)
- Priority Level (Select List)

### Step 3: Import Components
- Go to Project Settings → Components
- Add all 11 components manually

### Step 4: Import Epics
- Use CSV import feature
- Map fields correctly
- Set Epic issue type

### Step 5: Import Stories
- Link to parent epics
- Set story points
- Assign to team members

### Step 6: Import Subtasks
- Link to parent stories
- Set estimated hours
- Assign to developers

### Step 7: Configure Board
- Create Sprint board
- Add swimlanes (by epic)
- Configure columns (To Do, In Progress, Review, Done)

### Step 8: Setup Sprints
- Create 4 sprints (2 weeks each)
- Assign stories to sprints
- Set sprint goals

---

## ✅ VALIDATION CHECKLIST

After import, verify:
- [ ] All 54 epics imported
- [ ] All 200+ stories imported
- [ ] All 500+ subtasks imported
- [ ] Components assigned correctly
- [ ] Story points calculated
- [ ] Sprints configured
- [ ] Team members assigned
- [ ] Dependencies linked
- [ ] Priorities set correctly
- [ ] Board configured properly

---

**Ready to Import!** 🎉

This template is optimized for direct import into Jira using CSV or Excel formats.

---

*Last Updated: January 2025*  
*Project: Factory Production Management System*  
*Client: Factory Owner*  
*Vendor: Digital Awareness Foundation*
