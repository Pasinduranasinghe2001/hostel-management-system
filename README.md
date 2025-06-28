# Hostel Management System

This is a database management system (DBMS) project for managing hostel operations using **PHP**, **MySQL**, and **CSS**. The system includes role-based access for Admins, Wardens, and Students.

## 🔧 Technologies Used
- PHP
- MySQL (phpMyAdmin)
- HTML/CSS
- XAMPP (Apache & MySQL)

## 👤 User Roles & Functionalities

### 🧑 Student
- Register and log in
- Submit complaints
- Make payments
- Add visitor entries
- View personal complaints and payments
- Edit profile and change password

### 🧑‍💼 Warden
- Log in (via admin registration)
- View students, complaints, rooms, payments
- Assign, edit, or release rooms
- Mark complaints as resolved
- View visitor logs
- Edit profile and change password

### 🧑‍💼 Admin
- Log in
- Register students, wardens, and other admins
- View all users, complaints, payments
- Assign wardens
- Delete student accounts
- Edit profile and change password

## 🗃️ Folder Structure
Project Folder/
├── hostel_management/           ← All PHP, CSS, and interface files
├── Interfaces Screenshot/       ← Screenshots of UI and dashboards
└── hostel_management.sql        ← SQL file to import in phpMyAdmin


## 🚀 How to Run Locally
1. Install [XAMPP](https://www.apachefriends.org/)
2. Place the `hostel_management` folder in `htdocs`
3. Start **Apache** and **MySQL**
4. Open **phpMyAdmin** and import `hostel_management.sql`
5. Go to: [http://localhost/hostel_management](http://localhost/hostel_management)

## 📸 Screenshots
Screenshots of login, dashboards, and key pages are available in the **Interfaces Screenshot** folder.

## 📂 Database
The system uses MySQL with these main tables:
- `user`
- `student`
- `warden`
- `admin`
- `room`
- `complaint`
- `payment`
- `visitor_log`

## ✅ Features
- Role-based access control
- Specialization via `user_id` (admin, student, warden)
- Complaint and visitor tracking
- Payment handling and history
- Room management with occupancy tracking

## 🔒 Security
- Password hashing using `password_hash()`
- Session-based authentication
- Input validation for forms

## 📌 Future Improvements
- Email/SMS alerts
- Multi-hostel support
- Analytics dashboard
- Auto-reminders for late payments

## 📬 Author
- GitHub: [https://github.com/Pasinduranasinghe2001]
- University/Institution: [University of jaffna]
