/* =========================================================
   DATABASE RESET
   ========================================================= */
DROP DATABASE IF EXISTS college_tracking;
CREATE DATABASE college_tracking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE college_tracking;

/* =========================================================
   1. ROLES
   ========================================================= */
CREATE TABLE roles (
    role_id INT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO roles VALUES
(1,'ADMIN'),(2,'TEACHER'),(3,'STUDENT'),(4,'CR'),(5,'HOD');

/* =========================================================
   2. USERS (USN + DOB LOGIN)
   ========================================================= */
CREATE TABLE users (
    user_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usn VARCHAR(30) UNIQUE NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    dob DATE NOT NULL,
    role_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- Admin & HOD
INSERT INTO users VALUES
(NULL,'ADMIN001','Saanjali','Belgavi','1980-05-10',1,1,NOW()),
(NULL,'HOD001','Manjunath','Subhedar','1975-08-20',5,1,NOW());

/* =========================================================
   3. COURSES
   ========================================================= */
CREATE TABLE courses (
    course_id INT PRIMARY KEY,
    course_code VARCHAR(10),
    course_name VARCHAR(100),
    duration_years INT,
    total_semesters INT
);

INSERT INTO courses VALUES
(1,'BBA','Bachelor of Business Administration',3,6),
(2,'MBA','Master of Business Administration',2,4);

/* =========================================================
   4. SEMESTERS
   ========================================================= */
CREATE TABLE semesters (
    semester_id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    semester_number INT,
    UNIQUE(course_id, semester_number),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

INSERT INTO semesters (course_id,semester_number) VALUES
(1,1),(1,2),(1,3),(1,4),(1,5),(1,6),
(2,1),(2,2),(2,3),(2,4);

/* =========================================================
   5. CLASSES (A–D)
   ========================================================= */
CREATE TABLE classes (
    class_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    semester_id INT,
    section CHAR(1),
    academic_year VARCHAR(9),
    UNIQUE(course_id,semester_id,section,academic_year),
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id)
);

INSERT INTO classes (course_id,semester_id,section,academic_year)
SELECT s.course_id,s.semester_id,sec.section,'2024-2025'
FROM semesters s
JOIN (SELECT 'A' section UNION SELECT 'B' UNION SELECT 'C' UNION SELECT 'D') sec;

/* =========================================================
   6. SUBJECTS (5 PER SEMESTER – ENFORCED)
   ========================================================= */
CREATE TABLE subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    semester_id INT,
    subject_code VARCHAR(20),
    subject_name VARCHAR(100),
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id)
);

-- BBA subjects
INSERT INTO subjects (course_id,semester_id,subject_code,subject_name)
SELECT 1, semester_id, CONCAT('BBA',semester_number,'0',n), name
FROM semesters
JOIN (
  SELECT 1 n,'Management Principles' name UNION
  SELECT 2,'Business Economics' UNION
  SELECT 3,'Financial Accounting' UNION
  SELECT 4,'Business Statistics' UNION
  SELECT 5,'Business Communication'
) s
WHERE course_id=1;

-- MBA subjects
INSERT INTO subjects (course_id,semester_id,subject_code,subject_name)
SELECT 2, semester_id, CONCAT('MBA',semester_number,'0',n), name
FROM semesters
JOIN (
  SELECT 1 n,'Organizational Behaviour' name UNION
  SELECT 2,'Managerial Economics' UNION
  SELECT 3,'Financial Management' UNION
  SELECT 4,'Marketing Management' UNION
  SELECT 5,'Human Resource Management'
) s
WHERE course_id=2;

/* =========================================================
   7. TEACHERS (15)
   ========================================================= */
CREATE TABLE teachers (
    teacher_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNIQUE,
    department VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

INSERT INTO users (usn,first_name,last_name,dob,role_id) VALUES
('TCH001','Anil','Sharma','1980-01-01',2),
('TCH002','Meena','Iyer','1982-02-02',2),
('TCH003','Rajesh','Gupta','1979-03-03',2),
('TCH004','Sunita','Patil','1985-04-04',2),
('TCH005','Vikas','Reddy','1981-05-05',2),
('TCH006','Neeraj','Verma','1983-06-06',2),
('TCH007','Pankaj','Joshi','1978-07-07',2),
('TCH008','Smita','Nair','1986-08-08',2),
('TCH009','Kiran','Kulkarni','1984-09-09',2),
('TCH010','Suresh','Naidu','1977-10-10',2),
('TCH011','Asha','Desai','1982-11-11',2),
('TCH012','Manoj','Bansal','1980-12-12',2),
('TCH013','Ritu','Malhotra','1987-01-13',2),
('TCH014','Deepak','Chopra','1976-02-14',2),
('TCH015','Neha','Mehta','1988-03-15',2);

INSERT INTO teachers (user_id,department)
SELECT user_id,'Management' FROM users WHERE role_id=2;

/* =========================================================
   8. STUDENTS (AUTO-GENERATED)
   ========================================================= */
CREATE TABLE students (
    student_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNIQUE,
    course_id INT,
    admission_year YEAR,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

CREATE TABLE class_student_mapping (
    class_id BIGINT,
    student_id BIGINT,
    PRIMARY KEY (class_id,student_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- 30 students per class
INSERT INTO users (usn,first_name,last_name,dob,role_id)
SELECT CONCAT(
    IF(c.course_id=1,'BBA','MBA'),
    s.semester_number,
    c.section,
    LPAD(n,3,'0')
),
'Student',LPAD(n,3,'0'),'2004-01-01',3
FROM classes c
JOIN semesters s ON s.semester_id=c.semester_id
JOIN (
SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25
UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30
) nums;

INSERT INTO students (user_id,course_id,admission_year)
SELECT user_id,
CASE WHEN usn LIKE 'BBA%' THEN 1 ELSE 2 END,
2024
FROM users WHERE role_id=3;

INSERT INTO class_student_mapping
SELECT c.class_id,st.student_id
FROM classes c
JOIN semesters s ON s.semester_id=c.semester_id
JOIN users u ON u.usn LIKE CONCAT(IF(c.course_id=1,'BBA','MBA'),s.semester_number,c.section,'%')
JOIN students st ON st.user_id=u.user_id;

/* =========================================================
   9. TEACHER–SUBJECT–CLASS (FIXED)
   ========================================================= */
CREATE TABLE teacher_subject_mapping (
    mapping_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    teacher_id BIGINT,
    subject_id INT,
    class_id BIGINT,
    UNIQUE(subject_id,class_id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id)
);

-- Clear the table before inserting
DELETE FROM teacher_subject_mapping;

-- Then run your INSERT query
INSERT INTO teacher_subject_mapping (teacher_id, subject_id, class_id)
SELECT
    (SELECT teacher_id 
     FROM (
       SELECT teacher_id, (@rn := @rn + 1) as rn 
       FROM teachers, (SELECT @rn := 0) init 
       ORDER BY teacher_id
     ) ranked
     WHERE rn = (((c.class_id + s.subject_id) % (SELECT COUNT(*) FROM teachers)) + 1)
     LIMIT 1
    ) as teacher_id,
    s.subject_id,
    c.class_id
FROM classes c
JOIN subjects s
  ON s.course_id = c.course_id AND s.semester_id = c.semester_id
WHERE NOT EXISTS (
  SELECT 1 FROM teacher_subject_mapping tsm
  WHERE tsm.subject_id = s.subject_id AND tsm.class_id = c.class_id
);

/* =========================================================
   10. ATTENDANCE DATA
   ========================================================= */
CREATE TABLE attendance (
    attendance_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT,
    subject_id INT,
    attendance_date DATE,
    status ENUM('P','A'),
    UNIQUE(student_id,subject_id,attendance_date),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);

INSERT INTO attendance (student_id,subject_id,attendance_date,status)
SELECT st.student_id,s.subject_id,'2025-01-10',IF(RAND()>0.2,'P','A')
FROM class_student_mapping csm
JOIN students st ON st.student_id=csm.student_id
JOIN classes c ON c.class_id=csm.class_id
JOIN subjects s ON s.course_id=c.course_id AND s.semester_id=c.semester_id
LIMIT 3000;

/* =========================================================
   11. MARKS DATA
   ========================================================= */
CREATE TABLE assessments (
    assessment_id INT PRIMARY KEY,
    assessment_name VARCHAR(20),
    max_marks INT
);

INSERT INTO assessments VALUES
(1,'IA1',50),(2,'IA2',50),(3,'IA3',50);

CREATE TABLE marks (
    marks_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT,
    subject_id INT,
    assessment_id INT,
    marks_obtained INT,
    UNIQUE(student_id,subject_id,assessment_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id)
);

INSERT INTO marks (student_id,subject_id,assessment_id,marks_obtained)
SELECT st.student_id,s.subject_id,a.assessment_id,FLOOR(25+RAND()*25)
FROM class_student_mapping csm
JOIN students st ON st.student_id=csm.student_id
JOIN classes c ON c.class_id=csm.class_id
JOIN subjects s ON s.course_id=c.course_id AND s.semester_id=c.semester_id
JOIN assessments a
LIMIT 3000;

/* =========================================================
   12. SYLLABUS, CHAPTERS, CR
   ========================================================= */
CREATE TABLE syllabus (
    syllabus_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT,
    academic_year VARCHAR(9),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);

CREATE TABLE chapters (
    chapter_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    syllabus_id BIGINT,
    chapter_name VARCHAR(200),
    planned_end_date DATE,
    teacher_status ENUM('COMPLETED','DELAYED'),
    FOREIGN KEY (syllabus_id) REFERENCES syllabus(syllabus_id)
);

-- If syllabus_id is auto-increment and other columns have defaults
INSERT INTO syllabus (subject_id, academic_year)
SELECT subject_id, '2024-2025' 
FROM subjects;

INSERT INTO chapters (syllabus_id, chapter_name, planned_end_date, teacher_status)
SELECT syllabus_id, 'Introduction', '2025-02-01', 'COMPLETED'
FROM syllabus;

CREATE TABLE class_representatives (
    cr_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT,
    class_id BIGINT,
    subject_id INT,
    UNIQUE(class_id,subject_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);

INSERT INTO class_representatives (student_id,class_id,subject_id)
SELECT st.student_id,csm.class_id,tsm.subject_id
FROM teacher_subject_mapping tsm
JOIN class_student_mapping csm ON csm.class_id=tsm.class_id
JOIN students st ON st.student_id=csm.student_id
GROUP BY tsm.class_id,tsm.subject_id;

/* =========================================================
   13. CR CONFIRMATION
   ========================================================= */
CREATE TABLE cr_confirmations (
    confirmation_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    chapter_id BIGINT,
    cr_id BIGINT,
    confirmation_status ENUM('CONFIRMED','DELAYED','NOT_COMPLETED'),
    remarks VARCHAR(255),
    confirmation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chapter_id) REFERENCES chapters(chapter_id),
    FOREIGN KEY (cr_id) REFERENCES class_representatives(cr_id)
);

INSERT INTO cr_confirmations (chapter_id, cr_id, confirmation_status, remarks)
SELECT c.chapter_id, cr.cr_id, 'CONFIRMED', 'Completed on time'
FROM chapters c
CROSS JOIN class_representatives cr
LIMIT 50;

/* =========================================================
   14. PDF DOCUMENTS + VERSIONING
   ========================================================= */
CREATE TABLE generated_documents (
    document_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT,
    document_type ENUM('MARKSCARD','TC','MC'),
    current_version INT DEFAULT 1,
    status ENUM('DRAFT','APPROVED'),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

CREATE TABLE document_versions (
    version_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT,
    version_number INT,
    file_path VARCHAR(255),
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_id) REFERENCES generated_documents(document_id)
);

CREATE TABLE document_approvals (
    approval_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    document_id BIGINT,
    approved_by BIGINT,
    approved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_id) REFERENCES generated_documents(document_id),
    FOREIGN KEY (approved_by) REFERENCES users(user_id)
);

/* =========================================================
   15. AUDIT LOGS
   ========================================================= */
CREATE TABLE activity_logs (
    log_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    action VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
