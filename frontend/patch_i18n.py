import json, re, pathlib
base = pathlib.Path('/mnt/data/company_phase3')

# helper

def replace(path, old, new):
    p = base / path
    s = p.read_text(encoding='utf-8')
    if old not in s:
        print(f'MISSING in {path}: {old[:60]}')
    s = s.replace(old, new)
    p.write_text(s, encoding='utf-8')

# student-info.php
replace('student-info.php', '<title>QOYN | Student Info</title>', '<title data-i18n="student_info_page_title">QOYN | Student Info</title>\n  <script src="assets/js/i18n.js"></script>')
replace('student-info.php', '<li><a href="student-dashboard.php">Home</a></li>', '<li><a href="student-dashboard.php" data-i18n="home">Home</a></li>')
replace('student-info.php', '<li><a href="my_courses.php">My Courses</a></li>', '<li><a href="my_courses.php" data-i18n="my_courses">My Courses</a></li>')
replace('student-info.php', '<li><a href="my_project.php">Phase 2</a></li>', '<li><a href="my_project.php" data-i18n="phase2">Phase 2</a></li>')
replace('student-info.php', '<li><a href="my_capstone.php">Phase 3</a></li>', '<li><a href="my_capstone.php" data-i18n="phase3">Phase 3</a></li>')
replace('student-info.php', '<li><a href="student_chat.php">Chat</a></li>', '<li><a href="student_chat.php" data-i18n="chat">Chat</a></li>')
replace('student-info.php', '<li><a href="student-info.php" class="active">My Account</a></li>', '<li><a href="student-info.php" class="active" data-i18n="my_account">My Account</a></li>')
replace('student-info.php', '<li><a href="logout.php" class="nav-logout">Logout</a></li>', '<li><a href="logout.php" class="nav-logout" data-i18n="logout">Logout</a></li>')
replace('student-info.php', '<h1 class="page-title">My Account</h1>', '<h1 class="page-title" data-i18n="my_account">My Account</h1>')
replace('student-info.php', '<p class="profile-role">Student Account</p>', '<p class="profile-role" data-i18n="student_account">Student Account</p>')
replace('student-info.php', '<div class="info-label">Major</div>', '<div class="info-label" data-i18n="major">Major</div>')
replace('student-info.php', '<div class="info-label">Path</div>', '<div class="info-label" data-i18n="path">Path</div>')
replace('student-info.php', '<div class="info-label">Coins</div>', '<div class="info-label" data-i18n="coins">Coins</div>')
replace('student-info.php', '<div class="info-label">Email</div>', '<div class="info-label" data-i18n="email">Email</div>')
replace('student-info.php', '<a href="student-dashboard.php" class="btn btn-primary">Back to Dashboard</a>', '<a href="student-dashboard.php" class="btn btn-primary" data-i18n="back_to_dashboard">Back to Dashboard</a>')
replace('student-info.php', '<a href="my_courses.php" class="btn btn-light">My Courses</a>', '<a href="my_courses.php" class="btn btn-light" data-i18n="my_courses">My Courses</a>')
replace('student-info.php', '<a href="student-info.php?show=students" class="btn btn-light">All Students</a>', '<a href="student-info.php?show=students" class="btn btn-light" data-i18n="all_students">All Students</a>')
replace('student-info.php', '<a href="student-info.php?show=companies" class="btn btn-light">Companies</a>', '<a href="student-info.php?show=companies" class="btn btn-light" data-i18n="companies">Companies</a>')
replace('student-info.php', '<h3 class="list-title">All Students</h3>', '<h3 class="list-title" data-i18n="all_students">All Students</h3>')
replace('student-info.php', '<span class="person-short">Click to show details</span>', '<span class="person-short" data-i18n="click_show_details">Click to show details</span>')
replace('student-info.php', '<div class="info-label">Student ID</div>', '<div class="info-label" data-i18n="student_id">Student ID</div>')
replace('student-info.php', '<h3 class="list-title">Companies</h3>', '<h3 class="list-title" data-i18n="companies">Companies</h3>')
replace('student-info.php', '<div class="info-label">Company ID</div>', '<div class="info-label" data-i18n="company_id">Company ID</div>')
replace('student-info.php', '"Not available"', 't("not_available")')
replace('student-info.php', '"Not selected yet"', 't("not_selected_yet")')
replace('student-info.php', '"Student"', 't("student")')
replace('student-info.php', '"No name"', 't("no_name")')
replace('student-info.php', '"No email"', 't("no_email")')
replace('student-info.php', '"No students found."', 't("no_students_found")')
replace('student-info.php', '"No companies found."', 't("no_companies_found")')

# job_simulator.php
replace('job_simulator.php', '<title>QOYN | AI Job Simulator</title>', '<title data-i18n="job_simulator_page_title">QOYN | AI Job Simulator</title>')
replace('job_simulator.php', '<script defer src="assets/js/job_simulator.js"></script>', '<script src="assets/js/i18n.js"></script>\n  <script defer src="assets/js/job_simulator.js"></script>')
job_repls = {
'Dashboard':'nav_dashboard','My Page':'my_page','Courses':'all_courses','AI Job Simulator':'job_simulator_nav',
'Back to My Page':'back_to_my_page','AI Powered Career Check':'ai_powered_career_check','AI Job Readiness Simulator':'job_readiness_simulator_title',
'Upload your CV, add your GitHub or project link, choose your target role,\n                and get a more premium, visual, and exciting readiness experience that feels\n                like a real modern AI product.':'job_readiness_simulator_desc',
'Smart review for role-match and skill relevance':'smart_review_role_match','Checks your portfolio presence and project quality':'checks_portfolio_quality','Gives you a cleaner, faster job-readiness impression':'cleaner_faster_job_readiness',
'What it checks':'what_it_checks','The simulator reviews your selected role, your uploaded CV, and your public project link,\n          then prepares a structured readiness signal before sending you to the full report.':'simulator_checks_desc',
'Target role match':'target_role_match','CV review':'cv_review','GitHub / project evaluation':'github_project_evaluation','Professional Experience':'professional_experience','Cleaner layout, stronger visual hierarchy, and a more polished AI product feel.':'professional_experience_desc','Smarter Presentation':'smarter_presentation','Your simulator now looks more like a premium dashboard, not just a plain upload form.':'smarter_presentation_desc','Fast Navigation':'fast_navigation','Added a clear return button so the user can jump back to the main page instantly.':'fast_navigation_desc',
'Start your simulation':'start_your_simulation','Choose Target Role':'choose_target_role','Upload CV':'upload_cv','GitHub / Project Link':'github_project_link','https://github.com/username/project':'github_project_placeholder','Add your public GitHub repository or project link':'add_public_github_link','Start Analysis':'start_analysis','Premium look':'premium_look','Stronger gradients, cards, shapes, and illustration blocks make the simulator feel like a real AI platform instead of a plain form.':'premium_look_desc','Visual storytelling':'visual_storytelling','The page now explains itself visually through stats, side highlights, and a custom inline SVG illustration without needing external images.':'visual_storytelling_desc','Same backend':'same_backend','All your JavaScript IDs stay the same, so the logic keeps working while the whole page becomes much more impressive.':'same_backend_desc','How to look stronger in the simulation':'look_stronger_title','Small changes in your submission can make your profile look much better. Keep your CV clean, your GitHub public,\n          and your target role aligned with the skills you actually show.':'look_stronger_desc','Use a CV that clearly shows tools, projects, and measurable outcomes.':'look_stronger_tip_1','Add a public GitHub repo with a solid README and visible commits.':'look_stronger_tip_2','Pick a target role that actually matches your strongest technical skills.':'look_stronger_tip_3'
}
s = (base/'job_simulator.php').read_text(encoding='utf-8')
for text,key in job_repls.items():
    s = s.replace(f'>{text}<', f' data-i18n="{key}">{text}<')
# multiline p text exact tags
s = s.replace('<p>\n                Upload your CV, add your GitHub or project link, choose your target role,\n                and get a more premium, visual, and exciting readiness experience that feels\n                like a real modern AI product.\n              </p>', '<p data-i18n="job_readiness_simulator_desc">\n                Upload your CV, add your GitHub or project link, choose your target role,\n                and get a more premium, visual, and exciting readiness experience that feels\n                like a real modern AI product.\n              </p>')
s = s.replace('<p>\n          The simulator reviews your selected role, your uploaded CV, and your public project link,\n          then prepares a structured readiness signal before sending you to the full report.\n        </p>', '<p data-i18n="simulator_checks_desc">\n          The simulator reviews your selected role, your uploaded CV, and your public project link,\n          then prepares a structured readiness signal before sending you to the full report.\n        </p>')
s = s.replace('<input type="text" id="github_url" placeholder="https://github.com/username/project">', '<input type="text" id="github_url" data-i18n-placeholder="github_project_placeholder" placeholder="https://github.com/username/project">')
s = s.replace('<a class="back-link" href="index.php">Back to My Page</a>', '<a class="back-link" href="index.php" data-i18n="back_to_my_page">Back to My Page</a>')
(base/'job_simulator.php').write_text(s, encoding='utf-8')

# phase3_level2.php
replace('phase3_level2.php', '<title>QOYN | Phase 3 - Level 2</title>', '<title data-i18n="phase3_level2_page_title">QOYN | Phase 3 - Level 2</title>\n  <script src="assets/js/i18n.js"></script>')
phase_repls = {
'Phase 3 - Level 2':'phase3_level2_title','Submit':'submit','What happens here':'what_happens_here','Submit your explanation, repository URL, and any supporting file.':'phase3_level2_hero_desc',
'Challenge Type':'challenge_type','Difficulty':'difficulty','Required Actions':'required_actions','Deliverables':'deliverables','Submit Your Solution':'submit_your_solution','Ready to submit?':'ready_to_submit','Write your solution clearly, then upload a file or add your repository link if available.':'write_solution_clearly',
'Solution Explanation':'solution_explanation','Repository URL':'repository_url','Upload File':'upload_file','Submit Level 2':'submit_level2','Evaluation Result':'evaluation_result','Performance Score':'performance_score','Your final evaluation score appears here after submission.':'final_evaluation_score_here','Decision':'decision','Rubric Breakdown':'rubric_breakdown','Engineering Readiness':'engineering_readiness'
}
s = (base/'phase3_level2.php').read_text(encoding='utf-8')
for text,key in phase_repls.items():
    s = s.replace(f'>{text}<', f' data-i18n="{key}">{text}<')
s = s.replace('placeholder="اكتب شرح الحل هنا..."', 'data-i18n-placeholder="write_solution_here_placeholder" placeholder="اكتب شرح الحل هنا..."')
s = s.replace('<div class="helper">ارفع شرح الحل أو ملف أو رابط المستودع إن وجد.</div>', '<div class="helper" data-i18n="upload_explanation_or_file">ارفع شرح الحل أو ملف أو رابط المستودع إن وجد.</div>')
s = s.replace('<h2 id="challengeTitle">جاري تحميل التحدي...</h2>', '<h2 id="challengeTitle" data-default-i18n="loading_challenge">جاري تحميل التحدي...</h2>')
s = s.replace('يرجى الانتظار...', '<span data-i18n="please_wait">يرجى الانتظار...</span>')
s = s.replace('لا يوجد بيانات بعد', '<span data-i18n="no_data_yet">لا يوجد بيانات بعد</span>')
s = s.replace('لا يوجد تقييم بعد.', '<span data-i18n="no_evaluation_yet">لا يوجد تقييم بعد.</span>')
s = s.replace('No rubric data yet', '<span data-i18n="no_rubric_data_yet">No rubric data yet</span>')
s = s.replace('No readiness data yet', '<span data-i18n="no_readiness_data_yet">No readiness data yet</span>')
(base/'phase3_level2.php').write_text(s, encoding='utf-8')

# external JS job simulator
p = base/'assets/js/job_simulator.js'
s = p.read_text(encoding='utf-8')
repls = {
'"Starting simulation..."':'t("starting_simulation")',
'"Start failed: "':'t("start_failed") + ": "',
'"Uploading CV..."':'t("uploading_cv")',
'"CV upload failed: "':'t("cv_upload_failed") + ": "',
'"Saving project link..."':'t("saving_project_link")',
'"Project save failed: "':'t("project_save_failed") + ": "',
'"Running AI analysis..."':'t("running_ai_analysis")',
'"Analysis failed: "':'t("analysis_failed") + ": "',
'"Unexpected error happened."':'t("unexpected_error_happened")'
}
for a,b in repls.items(): s=s.replace(a,b)
p.write_text(s, encoding='utf-8')

# phase js dynamic replacements
p = base/'phase3_level2.php'
s = p.read_text(encoding='utf-8')
jsmap = {
'alert(data.error || "Failed to load challenge")':'alert(data.error || t("failed_to_load_challenge"))',
'alert(data.error || "Failed to generate challenge")':'alert(data.error || t("failed_to_generate_challenge"))',
'document.getElementById("challengeTitle").textContent = c.title || "Level 2 Challenge";':'document.getElementById("challengeTitle").textContent = c.title || t("level2_challenge");',
': `<li class="empty-note">لا يوجد actions</li>`;': ': `<li class="empty-note">${t("no_actions")}</li>`;',
': `<li class="empty-note">لا يوجد deliverables</li>`;': ': `<li class="empty-note">${t("no_deliverables")}</li>`;',
'alert("Unexpected error while loading challenge")':'alert(t("unexpected_error_loading_challenge"))',
'btn.textContent = "Submitting...";':'btn.textContent = t("submitting");',
'alert(data.error || "Submission failed")':'alert(data.error || t("submission_failed"))',
'btn.textContent = "Submit Level 2";':'btn.textContent = t("submit_level2");',
': `<li class="empty-note">No rubric data</li>`;': ': `<li class="empty-note">${t("no_rubric_data")}</li>`;',
': `<li class="empty-note">No readiness data</li>`;': ': `<li class="empty-note">${t("no_readiness_data")}</li>`;',
'alert("تم إرسال الحل وتقييمه بنجاح")':'alert(t("solution_submitted_evaluated_successfully"))',
'alert("تم حفظ التسليم بنجاح")':'alert(t("submission_saved_successfully"))',
'alert("Unexpected error while submitting")':'alert(t("unexpected_error_submitting"))'
}
for a,b in jsmap.items(): s=s.replace(a,b)
# initialize button text on language change and challenge placeholder
insert = '''\n    document.addEventListener("languageChanged", () => {\n      const btn = document.getElementById("submitBtn");\n      if (btn && !btn.disabled) btn.textContent = t("submit_level2");\n      const challengeTitle = document.getElementById("challengeTitle");\n      if (challengeTitle && !challengeTitle.dataset.loaded && !challengeTitle.textContent.trim()) {\n        challengeTitle.textContent = t("loading_challenge");\n      }\n    });\n'''
s = s.replace('    loadChallenge();\n  </script>', insert + '\n    loadChallenge();\n  </script>')
p.write_text(s, encoding='utf-8')

# add translations
for lang in ['ar','en']:
    p = base/f'assets/locales/{lang}.json'
    data = json.loads(p.read_text(encoding='utf-8'))
    adds = {
      'student_info_page_title': 'QOYN | معلومات الطالب' if lang=='ar' else 'QOYN | Student Info',
      'my_account': 'حسابي' if lang=='ar' else 'My Account',
      'student_account': 'حساب طالب' if lang=='ar' else 'Student Account',
      'major': 'التخصص' if lang=='ar' else 'Major',
      'path': 'المسار' if lang=='ar' else 'Path',
      'back_to_dashboard': 'العودة إلى لوحة الطالب' if lang=='ar' else 'Back to Dashboard',
      'all_students': 'كل الطلاب' if lang=='ar' else 'All Students',
      'click_show_details': 'اضغط لعرض التفاصيل' if lang=='ar' else 'Click to show details',
      'student_id': 'رقم الطالب' if lang=='ar' else 'Student ID',
      'company_id': 'رقم الشركة' if lang=='ar' else 'Company ID',
      'not_available': 'غير متوفر' if lang=='ar' else 'Not available',
      'not_selected_yet': 'لم يتم الاختيار بعد' if lang=='ar' else 'Not selected yet',
      'no_name': 'بدون اسم' if lang=='ar' else 'No name',
      'no_email': 'بدون بريد إلكتروني' if lang=='ar' else 'No email',
      'no_students_found': 'لا يوجد طلاب.' if lang=='ar' else 'No students found.',
      'no_companies_found': 'لا توجد شركات.' if lang=='ar' else 'No companies found.',
      'job_simulator_page_title': 'QOYN | محاكي الوظائف بالذكاء الاصطناعي' if lang=='ar' else 'QOYN | AI Job Simulator',
      'nav_dashboard': 'لوحة التحكم' if lang=='ar' else 'Dashboard',
      'job_simulator_nav': 'محاكي الوظائف بالذكاء الاصطناعي' if lang=='ar' else 'AI Job Simulator',
      'back_to_my_page': 'العودة إلى صفحتي' if lang=='ar' else 'Back to My Page',
      'ai_powered_career_check': 'فحص مهني مدعوم بالذكاء الاصطناعي' if lang=='ar' else 'AI Powered Career Check',
      'job_readiness_simulator_title': 'محاكي الجاهزية الوظيفية بالذكاء الاصطناعي' if lang=='ar' else 'AI Job Readiness Simulator',
      'job_readiness_simulator_desc': 'ارفع السيرة الذاتية، وأضف رابط GitHub أو المشروع، واختر الوظيفة المستهدفة للحصول على تجربة جاهزية وظيفية أكثر احترافية ووضوحًا وحداثة.' if lang=='ar' else 'Upload your CV, add your GitHub or project link, choose your target role, and get a more premium, visual, and exciting readiness experience that feels like a real modern AI product.',
      'smart_review_role_match': 'مراجعة ذكية لتوافق الدور والمهارات' if lang=='ar' else 'Smart review for role-match and skill relevance',
      'checks_portfolio_quality': 'يفحص حضور ملف الأعمال وجودة المشاريع' if lang=='ar' else 'Checks your portfolio presence and project quality',
      'cleaner_faster_job_readiness': 'يعطيك انطباعًا أوضح وأسرع عن جاهزيتك الوظيفية' if lang=='ar' else 'Gives you a cleaner, faster job-readiness impression',
      'what_it_checks': 'ماذا يفحص؟' if lang=='ar' else 'What it checks',
      'simulator_checks_desc': 'يقوم المحاكي بمراجعة الوظيفة التي اخترتها، والسيرة الذاتية التي رفعتها، ورابط المشروع العام، ثم يُحضّر إشارة جاهزية منظمة قبل نقلك إلى التقرير الكامل.' if lang=='ar' else 'The simulator reviews your selected role, your uploaded CV, and your public project link, then prepares a structured readiness signal before sending you to the full report.',
      'target_role_match': 'مدى تطابق الوظيفة المستهدفة' if lang=='ar' else 'Target role match',
      'cv_review': 'مراجعة السيرة الذاتية' if lang=='ar' else 'CV review',
      'github_project_evaluation': 'تقييم GitHub / المشروع' if lang=='ar' else 'GitHub / project evaluation',
      'professional_experience': 'تجربة احترافية' if lang=='ar' else 'Professional Experience',
      'professional_experience_desc': 'تصميم أوضح، وتسلسل بصري أقوى، وشعور أكثر احترافية كمنتج ذكاء اصطناعي.' if lang=='ar' else 'Cleaner layout, stronger visual hierarchy, and a more polished AI product feel.',
      'smarter_presentation': 'عرض أذكى' if lang=='ar' else 'Smarter Presentation',
      'smarter_presentation_desc': 'أصبح المحاكي يشبه لوحة تحكم احترافية أكثر من مجرد نموذج رفع عادي.' if lang=='ar' else 'Your simulator now looks more like a premium dashboard, not just a plain upload form.',
      'fast_navigation': 'تنقل سريع' if lang=='ar' else 'Fast Navigation',
      'fast_navigation_desc': 'تمت إضافة زر عودة واضح لينتقل المستخدم مباشرة إلى الصفحة الرئيسية.' if lang=='ar' else 'Added a clear return button so the user can jump back to the main page instantly.',
      'start_your_simulation': 'ابدأ المحاكاة' if lang=='ar' else 'Start your simulation',
      'choose_target_role': 'اختر الوظيفة المستهدفة' if lang=='ar' else 'Choose Target Role',
      'upload_cv': 'ارفع السيرة الذاتية' if lang=='ar' else 'Upload CV',
      'github_project_link': 'رابط GitHub / المشروع' if lang=='ar' else 'GitHub / Project Link',
      'github_project_placeholder': 'https://github.com/username/project',
      'add_public_github_link': 'أضف رابط مستودع GitHub أو مشروعك العام' if lang=='ar' else 'Add your public GitHub repository or project link',
      'start_analysis': 'ابدأ التحليل' if lang=='ar' else 'Start Analysis',
      'premium_look': 'مظهر احترافي' if lang=='ar' else 'Premium look',
      'premium_look_desc': 'تدرجات أقوى وبطاقات وأشكال ورسومات تجعل المحاكي يبدو كمنصة ذكاء اصطناعي حقيقية بدلًا من نموذج عادي.' if lang=='ar' else 'Stronger gradients, cards, shapes, and illustration blocks make the simulator feel like a real AI platform instead of a plain form.',
      'visual_storytelling': 'عرض بصري ذكي' if lang=='ar' else 'Visual storytelling',
      'visual_storytelling_desc': 'الصفحة أصبحت تشرح نفسها بصريًا عبر الإحصاءات والعناصر الجانبية ورسمة SVG داخلية بدون الحاجة لصور خارجية.' if lang=='ar' else 'The page now explains itself visually through stats, side highlights, and a custom inline SVG illustration without needing external images.',
      'same_backend': 'نفس الباك إند' if lang=='ar' else 'Same backend',
      'same_backend_desc': 'معرّفات JavaScript بقيت كما هي، لذلك المنطق يستمر بالعمل مع مظهر أكثر احترافية.' if lang=='ar' else 'All your JavaScript IDs stay the same, so the logic keeps working while the whole page becomes much more impressive.',
      'look_stronger_title': 'كيف تظهر بشكل أقوى في المحاكاة' if lang=='ar' else 'How to look stronger in the simulation',
      'look_stronger_desc': 'التعديلات الصغيرة في تسليمك قد تجعل ملفك يبدو أفضل بكثير. حافظ على سيرة ذاتية مرتبة، وGitHub عام، واختر وظيفة مستهدفة تتوافق فعلًا مع مهاراتك.' if lang=='ar' else 'Small changes in your submission can make your profile look much better. Keep your CV clean, your GitHub public, and your target role aligned with the skills you actually show.',
      'look_stronger_tip_1': 'استخدم سيرة ذاتية توضّح الأدوات والمشاريع والنتائج القابلة للقياس.' if lang=='ar' else 'Use a CV that clearly shows tools, projects, and measurable outcomes.',
      'look_stronger_tip_2': 'أضف مستودع GitHub عام مع README قوي وسجل commits واضح.' if lang=='ar' else 'Add a public GitHub repo with a solid README and visible commits.',
      'look_stronger_tip_3': 'اختر وظيفة مستهدفة تتوافق فعلًا مع أقوى مهاراتك التقنية.' if lang=='ar' else 'Pick a target role that actually matches your strongest technical skills.',
      'starting_simulation': 'بدء المحاكاة...' if lang=='ar' else 'Starting simulation...',
      'start_failed': 'فشل البدء' if lang=='ar' else 'Start failed',
      'uploading_cv': 'جارٍ رفع السيرة الذاتية...' if lang=='ar' else 'Uploading CV...',
      'cv_upload_failed': 'فشل رفع السيرة الذاتية' if lang=='ar' else 'CV upload failed',
      'saving_project_link': 'جارٍ حفظ رابط المشروع...' if lang=='ar' else 'Saving project link...',
      'project_save_failed': 'فشل حفظ المشروع' if lang=='ar' else 'Project save failed',
      'running_ai_analysis': 'جارٍ تشغيل تحليل الذكاء الاصطناعي...' if lang=='ar' else 'Running AI analysis...',
      'analysis_failed': 'فشل التحليل' if lang=='ar' else 'Analysis failed',
      'unexpected_error_happened': 'حدث خطأ غير متوقع.' if lang=='ar' else 'Unexpected error happened.',
      'phase3_level2_page_title': 'QOYN | المرحلة 3 - المستوى 2' if lang=='ar' else 'QOYN | Phase 3 - Level 2',
      'phase3_level2_title': 'المرحلة 3 - المستوى 2' if lang=='ar' else 'Phase 3 - Level 2',
      'what_happens_here': 'ماذا يحدث هنا' if lang=='ar' else 'What happens here',
      'phase3_level2_hero_desc': 'قدّم شرح الحل ورابط المستودع وأي ملف داعم.' if lang=='ar' else 'Submit your explanation, repository URL, and any supporting file.',
      'challenge_type': 'نوع التحدي' if lang=='ar' else 'Challenge Type',
      'required_actions': 'الإجراءات المطلوبة' if lang=='ar' else 'Required Actions',
      'submit_your_solution': 'قدّم الحل' if lang=='ar' else 'Submit Your Solution',
      'ready_to_submit': 'جاهز للتسليم؟' if lang=='ar' else 'Ready to submit?',
      'write_solution_clearly': 'اكتب الحل بوضوح ثم ارفع ملفًا أو أضف رابط المستودع إن وجد.' if lang=='ar' else 'Write your solution clearly, then upload a file or add your repository link if available.',
      'solution_explanation': 'شرح الحل' if lang=='ar' else 'Solution Explanation',
      'repository_url': 'رابط المستودع' if lang=='ar' else 'Repository URL',
      'upload_file': 'رفع ملف' if lang=='ar' else 'Upload File',
      'submit_level2': 'إرسال المستوى 2' if lang=='ar' else 'Submit Level 2',
      'evaluation_result': 'نتيجة التقييم' if lang=='ar' else 'Evaluation Result',
      'performance_score': 'درجة الأداء' if lang=='ar' else 'Performance Score',
      'final_evaluation_score_here': 'ستظهر درجة التقييم النهائية هنا بعد الإرسال.' if lang=='ar' else 'Your final evaluation score appears here after submission.',
      'rubric_breakdown': 'تفصيل المعايير' if lang=='ar' else 'Rubric Breakdown',
      'engineering_readiness': 'الجاهزية الهندسية' if lang=='ar' else 'Engineering Readiness',
      'write_solution_here_placeholder': 'اكتب شرح الحل هنا...' if lang=='ar' else 'Write your solution here...',
      'upload_explanation_or_file': 'ارفع شرح الحل أو ملفًا أو رابط المستودع إن وجد.' if lang=='ar' else 'Upload your explanation, file, or repository link if available.',
      'loading_challenge': 'جارٍ تحميل التحدي...' if lang=='ar' else 'Loading challenge...',
      'please_wait': 'يرجى الانتظار...' if lang=='ar' else 'Please wait...',
      'no_data_yet': 'لا يوجد بيانات بعد' if lang=='ar' else 'No data yet',
      'no_evaluation_yet': 'لا يوجد تقييم بعد.' if lang=='ar' else 'No evaluation yet.',
      'no_rubric_data_yet': 'لا يوجد بيانات تقييم بعد' if lang=='ar' else 'No rubric data yet',
      'no_readiness_data_yet': 'لا يوجد بيانات جاهزية بعد' if lang=='ar' else 'No readiness data yet',
      'failed_to_load_challenge': 'فشل تحميل التحدي' if lang=='ar' else 'Failed to load challenge',
      'failed_to_generate_challenge': 'فشل إنشاء التحدي' if lang=='ar' else 'Failed to generate challenge',
      'level2_challenge': 'تحدي المستوى 2' if lang=='ar' else 'Level 2 Challenge',
      'no_actions': 'لا يوجد إجراءات' if lang=='ar' else 'No actions',
      'no_deliverables': 'لا يوجد تسليمات' if lang=='ar' else 'No deliverables',
      'unexpected_error_loading_challenge': 'حدث خطأ غير متوقع أثناء تحميل التحدي' if lang=='ar' else 'Unexpected error while loading challenge',
      'submitting': 'جارٍ الإرسال...' if lang=='ar' else 'Submitting...',
      'submission_failed': 'فشل الإرسال' if lang=='ar' else 'Submission failed',
      'no_rubric_data': 'لا يوجد بيانات معايير' if lang=='ar' else 'No rubric data',
      'no_readiness_data': 'لا يوجد بيانات جاهزية' if lang=='ar' else 'No readiness data',
      'solution_submitted_evaluated_successfully': 'تم إرسال الحل وتقييمه بنجاح' if lang=='ar' else 'Solution submitted and evaluated successfully',
      'submission_saved_successfully': 'تم حفظ التسليم بنجاح' if lang=='ar' else 'Submission saved successfully',
      'unexpected_error_submitting': 'حدث خطأ غير متوقع أثناء الإرسال' if lang=='ar' else 'Unexpected error while submitting'
    }
    data.update(adds)
    p.write_text(json.dumps(data, ensure_ascii=False, indent=2), encoding='utf-8')

print('patched')
