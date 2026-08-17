<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Course::truncate();
        Section::truncate();
        Lesson::truncate();
        Schema::enableForeignKeyConstraints();

        $previewVideoUrl = 'https://www.w3schools.com/html/mov_bbb.mp4';

        // ============================================
        // COURSE 1: Machine Learning
        // ============================================
        $course1 = Course::create([
            'title' => 'Machine Learning',
            'slug' => Str::slug('Machine Learning'),
            'short_description' => 'Learn Regression, Classification, Clustering, Regularization, Optimization — 10 weeks.',
            'full_description' => '<h3>Overview</h3><p>Machine Learning is transforming industries across the globe. This comprehensive course takes you from foundational concepts to advanced techniques, equipping you with the skills to build intelligent systems that learn from data.</p><h3>Course Outline</h3><ul><li>Introduction To Machine Learning</li><li>Regression</li><li>Classification</li><li>Clustering</li><li>Optimization</li><li>Regularization</li><li>Complete Course Project</li><li>Get Your Machine Learning Certification</li></ul><h3>Skills You Will Learn</h3><ul><li>Data Modelling and Evaluation</li><li>Neural Networks</li><li>Communication Skills</li></ul><h3>Requirements</h3><ul><li>Basic Computer Skills</li><li>A Laptop with at least 2GB RAM</li><li>Readiness To Learn</li><li>Consistency</li></ul>',
            'price' => 200000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/1E3A5F/FFFFFF?text=Machine+Learning',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course1, $previewVideoUrl);

        // ============================================
        // COURSE 2: Frontend Web Development
        // ============================================
        $course2 = Course::create([
            'title' => 'Frontend Web Development',
            'slug' => Str::slug('Frontend Web Development'),
            'short_description' => 'Learn HTML, CSS and JavaScript for building websites — 8 weeks.',
            'full_description' => '<h3>Overview</h3><p>Welcome to our web design course! In this course, you will learn the essential skills and techniques required to create a modern, functional, and visually appealing website. Throughout the course, we will cover a range of topics, starting with the basics of web design and progressing to more advanced topics. You will learn the principles of web design, including layout, typography, color theory, and user experience (UX) design. We will also explore HTML, CSS, and JavaScript. By the end of this course, you will have a solid understanding of web design principles and be able to create your own stunning websites from scratch.</p><h3>Course Outline</h3><ul><li>Introduction To the Web</li><li>HTML Structure and HTML Elements</li><li>Semantic HTML</li><li>Styling Web Pages with CSS</li><li>Responsive Web Design</li><li>Programming Basics With Javascript</li><li>Document Object Model</li><li>Adding Interactivity to Web Pages using Javascript</li><li>Build Your Project</li><li>Get Your Web Design Certificate</li></ul><h3>Skills You Will Learn</h3><ul><li>UI/UX Design</li><li>Responsive Web Design</li><li>Basic Programming In Javascript</li></ul><h3>Requirements</h3><ul><li>Basic Computer Skills</li><li>A Laptop with at least 2GB RAM</li><li>Readiness To Learn</li><li>Consistency</li></ul>',
            'price' => 120000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/2563EB/FFFFFF?text=Frontend+Web',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course2, $previewVideoUrl);

        // ============================================
        // COURSE 3: Data Analysis Using SPSS
        // ============================================
        $course3 = Course::create([
            'title' => 'Data Analysis Using SPSS',
            'slug' => Str::slug('Data Analysis Using SPSS'),
            'short_description' => 'Learn data analysis and get insights from your data using SPSS — 8 weeks.',
            'full_description' => '<h3>Overview</h3><p>Learn steps for cleaning and preparing data for analysis — including handling missing values, formatting, normalizing and binning data. Perform exploratory data analysis and apply analytical techniques to real-world datasets. Manipulate data using dataframes, summarise data, understand data distribution, perform correlation. Build and evaluate regression models and use them for prediction and decision making.</p><h3>Course Outline</h3><ul><li>Introduction To Data Analysis</li><li>Data Collection</li><li>Data Cleaning</li><li>Data Transformation</li><li>Data Visualisation</li><li>Knowledge Discovery</li><li>Complete Course Project</li><li>Get Your Data Analysis Certification</li></ul><h3>What You Will Learn</h3><ul><li>Data Analysis</li><li>Data Visualisation</li><li>Model Selection</li></ul><h3>Requirements</h3><ul><li>Basic Computer Skills</li><li>A Laptop with at least 2GB RAM</li><li>Readiness To Learn</li><li>Consistency</li></ul>',
            'price' => 150000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/7C3AED/FFFFFF?text=SPSS',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course3, $previewVideoUrl);

        // ============================================
        // COURSE 4: Programming
        // ============================================
        $course4 = Course::create([
            'title' => 'Programming',
            'slug' => Str::slug('Programming'),
            'short_description' => 'Learn Java, Android, .NET, PHP, MySQL, JavaScript, Ajax, C++ — 8 weeks.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction To Programming</li><li>Basic Syntax and Making Comments</li><li>Variables, Data Types, Keywords and Statements</li><li>Expressions, Arithmetic Operators and Assignment Operators</li><li>Control Structures (Loops and Conditionals)</li><li>Data Structures</li><li>Functions</li><li>Objects and Classes</li><li>Build Your Project</li><li>Get Your Programming Certificate</li></ul><h3>Skills You Will Learn</h3><ul><li>Computational Thinking</li><li>Data Structures and Algorithms</li><li>Object Oriented Programming</li></ul><h3>Requirements</h3><ul><li>Basic Computer Skills</li><li>A Laptop with at least 2GB RAM</li><li>Readiness To Learn</li><li>Consistency</li></ul>',
            'price' => 180000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/059669/FFFFFF?text=Programming',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course4, $previewVideoUrl);

        // ============================================
        // COURSE 5: Backend Web Development
        // ============================================
        $course5 = Course::create([
            'title' => 'Backend Web Development',
            'slug' => Str::slug('Backend Web Development'),
            'short_description' => 'Learn PHP, SQL and Laravel for Backend Development — 8 weeks.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction To Programming</li><li>Basic Syntax and Making Comments</li><li>Variables, Data Types, Keywords and Statements</li><li>Expressions, Arithmetic Operators and Assignment Operators</li><li>Control Structures (Loops and Conditionals)</li><li>Data Structures</li><li>Functions</li><li>Objects and Classes</li><li>Build Your Project</li><li>Get Your Programming Certificate</li></ul><h3>Skills You Will Learn</h3><ul><li>Computational Thinking</li><li>Data Structures and Algorithms</li><li>Object Oriented Programming</li></ul><h3>Requirements</h3><ul><li>Basic Computer Skills</li><li>A Laptop with at least 2GB RAM</li><li>Readiness To Learn</li><li>Consistency</li></ul>',
            'price' => 150000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/DC2626/FFFFFF?text=Backend+Web',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course5, $previewVideoUrl);

        // ============================================
        // COURSE 6: Operating System
        // ============================================
        $course6 = Course::create([
            'title' => 'Operating System',
            'slug' => Str::slug('Operating System'),
            'short_description' => 'Learn Linux, Unix, Windows, or DOS — choose your path.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction To Operating Systems</li><li>Installation</li><li>GUI Basics</li><li>Navigating the Environment</li><li>File System</li><li>Security</li><li>Applications</li><li>Web Browsing</li><li>Media Management</li><li>Get Your Operating System Certification</li></ul><h3>Skills You Will Learn</h3><ul><li>Working with Files, Folders and Cloud Storage</li><li>Windows Security</li><li>Basic OS Troubleshooting</li></ul><h3>Requirements</h3><ul><li>Basic Computer Skills</li><li>A Laptop with at least 2GB RAM</li><li>Readiness To Learn</li><li>Consistency</li></ul>',
            'price' => 100000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/D97706/FFFFFF?text=Operating+System',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course6, $previewVideoUrl);

        // ============================================
        // COURSE 7: Business Analysis
        // ============================================
        $course7 = Course::create([
            'title' => 'Business Analysis',
            'slug' => Str::slug('Business Analysis'),
            'short_description' => 'Unlock the secrets of business success. Analyze, strategize, and drive growth.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction to Business Analysis</li><li>Understanding Business Models</li><li>Market Research and Analysis</li><li>Financial Analysis and Reporting</li><li>Operational Analysis and Optimization</li><li>Project Management for Business Analysts</li><li>Communication and Presentation Skills</li><li>Case Studies and Practical Applications</li></ul><h3>Skills You Will Learn</h3><ul><li>Data analysis techniques</li><li>Market research methodologies</li><li>Financial modeling and reporting</li><li>Strategic thinking and decision-making</li><li>Project management fundamentals</li><li>Effective communication and presentation skills</li></ul><h3>Requirements</h3><ul><li>Basic understanding of business concepts</li><li>Proficiency in Microsoft Excel and PowerPoint</li><li>Access to a computer with internet connection</li><li>Eagerness to learn and apply new skills to real-world scenarios</li></ul><p>Enroll now and take the first step towards becoming a proficient business analyst, ready to tackle challenges and drive success in any industry.</p>',
            'price' => 150000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/4F46E5/FFFFFF?text=Business+Analysis',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course7, $previewVideoUrl);

        // ============================================
        // COURSE 8: Research Methodology
        // ============================================
        $course8 = Course::create([
            'title' => 'Research Methodology',
            'slug' => Str::slug('Research Methodology'),
            'short_description' => 'Master the art and science of research. Design, conduct, and analyze research effectively.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction to Research Methodology</li><li>Formulating Research Questions and Objectives</li><li>Research Design and Sampling Techniques</li><li>Data Collection Methods: Surveys, Interviews, and Observations</li><li>Data Analysis: Quantitative and Qualitative Approaches</li><li>Interpretation and Presentation of Research Findings</li><li>Ethical Considerations in Research</li><li>Case Studies and Practical Applications</li></ul><h3>Skills You Will Learn</h3><ul><li>Formulating clear and focused research questions</li><li>Designing robust research methodologies</li><li>Implementing various data collection techniques</li><li>Analyzing data using statistical and qualitative methods</li><li>Interpreting research findings accurately</li><li>Presenting research results effectively</li></ul><h3>Requirements</h3><ul><li>Basic understanding of research concepts</li><li>Familiarity with Microsoft Excel and statistical software (e.g., SPSS, R)</li><li>Access to resources for data collection</li><li>Commitment to ethical research conduct</li><li>Passion for acquiring new knowledge and skills in research methodology</li></ul><p>Enroll now and embark on a journey to become a proficient researcher.</p>',
            'price' => 120000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/0891B2/FFFFFF?text=Research+Methodology',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course8, $previewVideoUrl);

        // ============================================
        // COURSE 9: Software Engineering
        // ============================================
        $course9 = Course::create([
            'title' => 'Software Engineering',
            'slug' => Str::slug('Software Engineering'),
            'short_description' => 'Learn principles, methodologies, and best practices for developing high-quality software solutions.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction to Software Engineering</li><li>Software Development Life Cycle (SDLC)</li><li>Requirements Engineering and Analysis</li><li>Software Design Principles and Patterns</li><li>Programming Paradigms and Languages</li><li>Testing and Quality Assurance</li><li>Version Control and Collaboration Tools</li><li>Agile and DevOps Methodologies</li><li>Software Maintenance and Evolution</li><li>Case Studies and Real-world Applications</li></ul><h3>Skills You Will Learn</h3><ul><li>Understanding of software engineering principles and practices</li><li>Proficiency in various programming languages and paradigms</li><li>Ability to design scalable and maintainable software solutions</li><li>Expertise in testing and quality assurance techniques</li><li>Familiarity with agile and DevOps methodologies</li><li>Collaboration and communication skills for effective team-based development</li></ul><h3>Requirements</h3><ul><li>Basic knowledge of programming concepts</li><li>Access to a computer with internet connectivity</li><li>Willingness to learn and adapt to new technologies</li><li>Eagerness to collaborate with peers and work in team environments</li><li>Passion for building innovative software solutions</li></ul><p>Enroll now and embark on a rewarding journey to become a skilled software engineer.</p>',
            'price' => 200000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/6D28D9/FFFFFF?text=Software+Engineering',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course9, $previewVideoUrl);

        // ============================================
        // COURSE 10: Software Testing
        // ============================================
        $course10 = Course::create([
            'title' => 'Software Testing',
            'slug' => Str::slug('Software Testing'),
            'short_description' => 'Gain expertise in ensuring quality and reliability of software products through comprehensive testing.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction to Software Testing</li><li>Software Development Life Cycle (SDLC) and Testing</li><li>Types of Testing: Functional, Non-functional, and Regression Testing</li><li>Test Planning and Documentation</li><li>Test Case Design and Execution</li><li>Automated Testing Tools and Techniques</li><li>Performance Testing and Load Testing</li><li>Security Testing and Penetration Testing</li><li>Usability Testing and User Acceptance Testing</li><li>Defect Tracking and Management</li><li>Continuous Integration and Continuous Testing</li><li>Case Studies and Real-world Scenarios</li></ul><h3>Skills You Will Learn</h3><ul><li>Understanding of software testing principles and methodologies</li><li>Proficiency in creating test plans, test cases, and test scripts</li><li>Hands-on experience with automated testing tools and frameworks</li><li>Ability to conduct various types of testing</li><li>Expertise in identifying, reporting, and managing software defects</li><li>Knowledge of best practices for integrating testing into the software development process</li></ul><h3>Requirements</h3><ul><li>Basic understanding of software development concepts</li><li>Familiarity with at least one programming language (e.g., Java, Python)</li><li>Access to a computer with internet connectivity</li><li>Eagerness to learn and explore new testing techniques and tools</li><li>Strong attention to detail and analytical skills</li></ul><p>Enroll now and become a proficient software tester.</p>',
            'price' => 130000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/0E7490/FFFFFF?text=Software+Testing',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course10, $previewVideoUrl);

        // ============================================
        // COURSE 11: Cyber Security
        // ============================================
        $course11 = Course::create([
            'title' => 'Cyber Security',
            'slug' => Str::slug('Cyber Security'),
            'short_description' => 'Protect organizations from cyber threats and safeguard sensitive information in a digital world.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction to Cybersecurity</li><li>Cyber Threat Landscape and Attack Vectors</li><li>Security Fundamentals: Confidentiality, Integrity, and Availability (CIA)</li><li>Cryptography and Encryption Techniques</li><li>Network Security: Firewalls, IDS, and IPS</li><li>Secure Software Development Practices</li><li>Web Security: Common Vulnerabilities and Best Practices</li><li>Identity and Access Management (IAM)</li><li>Incident Response and Disaster Recovery</li><li>Compliance and Regulatory Requirements</li><li>Emerging Trends in Cybersecurity</li><li>Case Studies and Real-world Scenarios</li></ul><h3>Skills You Will Learn</h3><ul><li>Understanding of cybersecurity concepts, principles, and best practices</li><li>Proficiency in implementing security measures to protect networks, systems, and data</li><li>Knowledge of encryption techniques and cryptographic protocols</li><li>Ability to identify and mitigate common cybersecurity threats and vulnerabilities</li><li>Expertise in incident response and disaster recovery planning</li><li>Familiarity with compliance standards and regulatory requirements</li></ul><h3>Requirements</h3><ul><li>Basic understanding of computer networks and information technology</li><li>Access to a computer with internet connectivity</li><li>Eagerness to learn and stay updated on evolving cybersecurity threats</li><li>Strong problem-solving and analytical skills</li><li>Commitment to upholding ethical standards</li></ul>',
            'price' => 200000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/991B1B/FFFFFF?text=Cyber+Security',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course11, $previewVideoUrl);

        // ============================================
        // COURSE 12: Data Analysis Using Python
        // ============================================
        $course12 = Course::create([
            'title' => 'Data Analysis Using Python',
            'slug' => Str::slug('Data Analysis Using Python'),
            'short_description' => 'Harness Python libraries like NumPy, Pandas, and Matplotlib to analyze data and make informed decisions.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction to Statistical Analysis and Python</li><li>Data Manipulation with Pandas</li><li>Descriptive Statistics and Data Visualization with Matplotlib and Seaborn</li><li>Probability Distributions and Random Variables</li><li>Hypothesis Testing and Statistical Inference</li><li>Correlation and Regression Analysis</li><li>Time Series Analysis</li><li>Multivariate Analysis and Dimensionality Reduction</li><li>Machine Learning for Statistical Analysis</li><li>Case Studies and Real-world Applications</li></ul><h3>Skills You Will Learn</h3><ul><li>Understanding of statistical concepts and methods</li><li>Proficiency in data manipulation and analysis using Pandas</li><li>Ability to visualize data effectively using Matplotlib and Seaborn</li><li>Knowledge of probability theory and its applications</li><li>Expertise in hypothesis testing and statistical inference</li><li>Familiarity with advanced statistical techniques and machine learning algorithms</li></ul><h3>Requirements</h3><ul><li>Basic knowledge of Python programming</li><li>Familiarity with data structures like lists, tuples, and dictionaries</li><li>Access to a computer with Python and relevant libraries installed</li><li>Eagerness to learn and apply statistical techniques to real-world datasets</li><li>Strong analytical and problem-solving skills</li></ul>',
            'price' => 150000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/2563EB/FFFFFF?text=Python+Data+Analysis',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course12, $previewVideoUrl);

        // ============================================
        // COURSE 13: Data Analysis Using R
        // ============================================
        $course13 = Course::create([
            'title' => 'Data Analysis Using R',
            'slug' => Str::slug('Data Analysis Using R'),
            'short_description' => 'Leverage R\'s ecosystem of packages for data manipulation, visualization, and hypothesis testing.',
            'full_description' => '<h3>Course Outline</h3><ul><li>Introduction to Data Analysis and R</li><li>Data Manipulation with dplyr and tidyr</li><li>Data Visualization with ggplot2</li><li>Descriptive Statistics and Exploratory Data Analysis (EDA)</li><li>Probability Distributions and Sampling</li><li>Hypothesis Testing and Confidence Intervals</li><li>Correlation and Regression Analysis</li><li>Multivariate Analysis</li><li>Machine Learning for Statistical Analysis with caret</li><li>Case Studies and Real-world Applications</li></ul><h3>Skills You Will Learn</h3><ul><li>Understanding of statistical concepts and methods</li><li>Proficiency in data manipulation using dplyr and tidyr</li><li>Ability to create insightful visualizations with ggplot2</li><li>Knowledge of probability theory and its applications in R</li><li>Expertise in hypothesis testing and interpreting results</li><li>Familiarity with advanced statistical techniques and machine learning algorithms using caret</li></ul><h3>Requirements</h3><ul><li>Basic understanding of statistics</li><li>Familiarity with programming concepts (R experience is a plus but not required)</li><li>Access to a computer with R and RStudio installed</li><li>Eagerness to learn and apply statistical techniques to real-world datasets</li><li>Strong analytical and problem-solving skills</li></ul>',
            'price' => 150000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/059669/FFFFFF?text=R+Data+Analysis',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course13, $previewVideoUrl);

        // ============================================
        // COURSE 14: Basic Computer Appreciation
        // ============================================
        $course14 = Course::create([
            'title' => 'Basic Computer Appreciation',
            'slug' => Str::slug('Basic Computer Appreciation'),
            'short_description' => 'Learn Microsoft Word, Microsoft Excel and Microsoft PowerPoint — 8 weeks.',
            'full_description' => '<h3>Overview</h3><p>Master the essential Microsoft Office applications used in businesses worldwide.</p><h3>Course Outline</h3><ul><li>Microsoft Word</li><li>Microsoft Excel</li><li>Microsoft PowerPoint</li><li>The Internet</li><li>Complete Your Project</li><li>Get Your Certificate</li></ul><h3>Skills You Will Learn</h3><ul><li>Typing Skills</li><li>Word Processing skills</li><li>Excel skills</li></ul><h3>Requirements</h3><ul><li>A PC with at least 1GB RAM</li><li>Readiness To Learn</li><li>Consistency</li></ul>',
            'price' => 80000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/F59E0B/FFFFFF?text=Computer+Appreciation',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course14, $previewVideoUrl);

        // ============================================
        // COURSE 15: Graphic Design: Corel Draw + Photoshop
        // ============================================
        $course15 = Course::create([
            'title' => 'Graphic Design: Corel Draw + Photoshop',
            'slug' => Str::slug('Graphic Design Corel Draw Photoshop'),
            'short_description' => 'Learn how to create logos, banners and illustrations with Corel Draw and Photoshop — 8 weeks.',
            'full_description' => '<h3>Overview</h3><p>This course will teach you the fundamentals of graphic design using two of the most popular software tools in the industry — Corel Draw and Photoshop. By the end of the course, you will have a strong foundation in graphic design principles and the technical skills needed to create professional-grade designs.</p><h3>Course Outline</h3><ul><li>Understanding design principles</li><li>Elements of design</li><li>Introduction to the Corel Draw interface</li><li>Creating shapes and objects</li><li>Adding and manipulating text</li><li>Logo Design</li><li>Introduction to the Photoshop interface</li><li>Image manipulation and correction</li><li>Final project presentation and feedback</li><li>Get Your Graphic Design Certificate</li></ul><h3>What You Will Learn</h3><ul><li>Understanding of graphic design principles</li><li>Proficiency in CorelDraw</li><li>Proficiency in Photoshop</li><li>Design Experience</li></ul><h3>Requirements</h3><ul><li>Basic Computer Skills</li><li>A Laptop with at least 2GB RAM</li><li>Readiness To Learn</li><li>Consistency</li></ul>',
            'price' => 100000,
            'async_price' => 15000,
            'lesson_min_minutes' => 1,
            'thumbnail_path' => 'https://placehold.co/600x400/EC4899/FFFFFF?text=Graphic+Design',
            'is_published' => true,
        ]);
        $this->createStandardCurriculum($course15, $previewVideoUrl);

        $this->command->info('15 courses with full descriptions seeded successfully!');
    }

    private function createStandardCurriculum(Course $course, string $previewVideoUrl): void
    {
        $section1 = Section::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'order' => 1,
        ]);

        Lesson::create([
            'section_id' => $section1->id,
            'title' => 'Welcome to ' . $course->title,
            'content_type' => 'video',
            'content_path' => $previewVideoUrl,
            'content_body' => '<p>Welcome to this course! This introductory lesson covers what you will learn, how the course is structured, and tips for success.</p>',
            'duration' => 180,
            'order' => 1,
            'is_free_preview' => true,
        ]);

        Lesson::create([
            'section_id' => $section1->id,
            'title' => 'Course Overview & Learning Objectives',
            'content_type' => 'text',
            'content_body' => '<h3>What You\'ll Achieve</h3><p>By the end of this course, you will have mastered the core concepts and completed practical projects.</p>',
            'duration' => 240,
            'order' => 2,
            'is_free_preview' => true,
        ]);

        Lesson::create([
            'section_id' => $section1->id,
            'title' => 'Setting Up Your Environment',
            'content_type' => 'text',
            'content_body' => '<h3>Tools & Setup</h3><p>Before we dive in, make sure you have all the necessary tools installed and configured.</p>',
            'duration' => 300,
            'order' => 3,
        ]);

        $section2 = Section::create([
            'course_id' => $course->id,
            'title' => 'Core Concepts & Fundamentals',
            'order' => 2,
        ]);

        Lesson::create([
            'section_id' => $section2->id,
            'title' => 'Understanding the Foundations',
            'content_type' => 'video',
            'content_path' => $previewVideoUrl,
            'content_body' => '<p>This lesson covers the foundational principles. Master these building blocks.</p>',
            'duration' => 480,
            'order' => 1,
        ]);

        Lesson::create([
            'section_id' => $section2->id,
            'title' => 'Key Techniques & Methods',
            'content_type' => 'text',
            'content_body' => '<h3>Essential Techniques</h3><p>Learn the key methods and techniques used by professionals.</p>',
            'duration' => 540,
            'order' => 2,
        ]);

        $section3 = Section::create([
            'course_id' => $course->id,
            'title' => 'Practical Projects & Certification',
            'order' => 3,
        ]);

        Lesson::create([
            'section_id' => $section3->id,
            'title' => 'Hands-On Project',
            'content_type' => 'video',
            'content_path' => $previewVideoUrl,
            'content_body' => '<p>Apply everything you\'ve learned in a comprehensive hands-on project.</p>',
            'duration' => 720,
            'order' => 1,
        ]);

        Lesson::create([
            'section_id' => $section3->id,
            'title' => 'Course Wrap-Up & Next Steps',
            'content_type' => 'text',
            'content_body' => '<h3>Congratulations!</h3><p>You\'ve completed the course. Here\'s how to claim your certificate.</p>',
            'duration' => 180,
            'order' => 2,
        ]);
    }
}