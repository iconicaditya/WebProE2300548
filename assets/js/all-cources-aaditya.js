(function () {
	const pageRoot = document.querySelector('.courses-catalog-page');
	if (!pageRoot) return;

	const baseUrl = window.eduSkillBaseUrl || '/';
	const IMAGES_BASE = baseUrl + 'assets/images/cources/';
	const courses = [
		{ id:'c1',  image: IMAGES_BASE+'web-dev.jpg',         title:'Full-Stack Web Development Bootcamp',         category:'Programming',    level:'Beginner',     duration:'10 Weeks', price:'$129', instructor:'Aaditya Sharma',  rating:'4.8', students:'3,200', overview:'Build responsive websites and web apps from scratch using HTML, CSS, JavaScript, PHP, and MySQL.', syllabus:['Modern HTML5 and semantic structure','Responsive CSS and component layouts','JavaScript DOM and API integration','PHP + MySQL mini-project deployment'], tools:['VS Code','XAMPP','Git/GitHub','Figma Basics'], outcomes:['Create production-ready portfolio projects','Understand full request-response workflow','Deploy and present complete web apps'] },
		{ id:'c2',  image: IMAGES_BASE+'data-analytics.jpg',   title:'Data Analytics With Python and Power BI',     category:'Data Science',   level:'Intermediate', duration:'8 Weeks',  price:'$149', instructor:'Nisha Koirala',   rating:'4.9', students:'2,050', overview:'Clean, analyze, and visualize business data to make better decisions through dashboards and reports.', syllabus:['NumPy and Pandas workflows','Exploratory data analysis','Power BI dashboard design','Storytelling with data case study'], tools:['Python','Jupyter','Power BI','Excel'], outcomes:['Build KPI dashboards','Perform end-to-end analytics workflow','Communicate insights to stakeholders'] },
		{ id:'c3',  image: IMAGES_BASE+'ui-ux.jpg',            title:'UI/UX Design for Digital Products',           category:'Design',         level:'Beginner',     duration:'6 Weeks',  price:'$99',  instructor:'Karan Basnet',    rating:'4.7', students:'1,480', overview:'Design user-centered interfaces with strong visual hierarchy, accessibility, and practical design systems.', syllabus:['UX research and user personas','Wireframing and prototyping','Visual design system foundations','Usability testing and iteration'], tools:['Figma','FigJam','Notion','Maze'], outcomes:['Create polished app/web screens','Run practical UX tests','Present a complete case study portfolio'] },
		{ id:'c4',  image: IMAGES_BASE+'digital-marketing.jpg',title:'Strategic Digital Marketing Masterclass',     category:'Business',       level:'Advanced',     duration:'7 Weeks',  price:'$139', instructor:'Ritika Adhikari', rating:'4.8', students:'2,760', overview:'Plan, launch, and optimize marketing campaigns using SEO, paid media, social channels, and analytics.', syllabus:['Campaign strategy and funnel design','SEO and content planning','Ads optimization and conversion metrics','Marketing performance reporting'], tools:['GA4','Meta Ads','Google Ads','Canva'], outcomes:['Run measurable digital campaigns','Optimize CAC and ROAS','Build a full-funnel marketing plan'] },
		{ id:'c5',  image: IMAGES_BASE+'react-frontend.jpg',   title:'React.js & Modern Frontend Development',      category:'Programming',    level:'Intermediate', duration:'8 Weeks',  price:'$119', instructor:'Saurav Pandey',   rating:'4.7', students:'2,140', overview:'Master React hooks, state management, routing, and deployment to build real-world single-page applications.', syllabus:['React components and JSX','Hooks: useState, useEffect, useContext','React Router and code splitting','Redux Toolkit state management'], tools:['React','Vite','Redux','Tailwind CSS'], outcomes:['Build and deploy full SPAs','Manage complex app state','Integrate REST APIs with React'] },
		{ id:'c6',  image: IMAGES_BASE+'machine-learning.jpg', title:'Machine Learning Fundamentals with Python',   category:'Data Science',   level:'Advanced',     duration:'10 Weeks', price:'$179', instructor:'Priya Dhakal',    rating:'4.9', students:'1,820', overview:'Learn supervised and unsupervised machine learning algorithms, model evaluation, and deployment pipelines.', syllabus:['Linear and logistic regression','Decision trees & random forests','Clustering and dimensionality reduction','Model deployment with Flask/FastAPI'], tools:['Python','scikit-learn','TensorFlow','Jupyter'], outcomes:['Build and evaluate ML models','Deploy prediction APIs','Interpret model results'] },
		{ id:'c7',  image: IMAGES_BASE+'graphic-design.jpg',   title:'Graphic Design with Adobe Creative Suite',    category:'Design',         level:'Beginner',     duration:'6 Weeks',  price:'$89',  instructor:'Anita Thapa',     rating:'4.6', students:'1,630', overview:'Master Photoshop, Illustrator, and InDesign to produce professional-grade brand assets and print/digital media.', syllabus:['Photoshop layer masking and retouching','Illustrator vector illustration','InDesign layout and typography','Brand identity design project'], tools:['Photoshop','Illustrator','InDesign','Adobe Fonts'], outcomes:['Design logos and brand kits','Create print-ready materials','Build a design portfolio'] },
		{ id:'c8',  image: IMAGES_BASE+'ecommerce.jpg',        title:'E-Commerce Business Blueprint',               category:'Business',       level:'Intermediate', duration:'6 Weeks',  price:'$109', instructor:'Mohan Gurung',    rating:'4.6', students:'1,350', overview:'Launch and scale an online store using Shopify or WooCommerce with effective product listings, SEO, and ad strategies.', syllabus:['Platform setup and theme customization','Product research and catalog optimization','Checkout and payment gateway setup','E-commerce SEO and ROAS'], tools:['Shopify','WooCommerce','GA4','Klaviyo'], outcomes:['Launch a profitable online store','Drive organic and paid traffic','Optimize conversions and checkout'] },
		{ id:'c9',  image: IMAGES_BASE+'mobile-dev.jpg',       title:'iOS & Android App Development',               category:'Programming',    level:'Intermediate', duration:'9 Weeks',  price:'$149', instructor:'Rajan Poudel',    rating:'4.7', students:'1,760', overview:'Build cross-platform mobile apps with React Native and publish them to App Store and Google Play.', syllabus:['React Native setup and components','Navigation with Expo Router','Platform APIs: camera, storage, push notifications','App Store submission workflow'], tools:['React Native','Expo','Android Studio','TestFlight'], outcomes:['Ship an app to both stores','Handle device APIs','Build offline-first mobile UIs'] },
		{ id:'c10', image: IMAGES_BASE+'cybersecurity.jpg',    title:'Cybersecurity Essentials for Beginners',      category:'Cybersecurity',  level:'Beginner',     duration:'5 Weeks',  price:'$79',  instructor:'Deepak Rana',     rating:'4.5', students:'2,210', overview:'Understand cyber threats, secure network design, encryption fundamentals, and safe browsing practices.', syllabus:['Threat landscape and attack vectors','Network security basics','Password management and MFA','Phishing and social engineering defense'], tools:['Wireshark','Kali Linux','VPN','1Password'], outcomes:['Identify common cyber threats','Secure home and work networks','Follow security best practices'] },
		{ id:'c11', image: IMAGES_BASE+'python.jpg',           title:'Python Programming for Absolute Beginners',   category:'Programming',    level:'Beginner',     duration:'5 Weeks',  price:'$69',  instructor:'Suresh Byanjankar', rating:'4.8', students:'4,100', overview:'Start coding in Python from scratch with hands-on exercises covering variables, loops, functions, and OOP.', syllabus:['Variables, data types, operators','Control flow and loops','Functions and modules','Object-oriented programming basics'], tools:['Python','VS Code','Jupyter','Replit'], outcomes:['Write clean Python scripts','Build small automation tools','Understand foundational OOP concepts'] },
		{ id:'c12', image: IMAGES_BASE+'video-editing.jpg',    title:'Video Editing & Post-Production',             category:'Design',         level:'Beginner',     duration:'5 Weeks',  price:'$89',  instructor:'Rina Tamrakar',   rating:'4.6', students:'1,220', overview:'Learn professional video editing, color grading, and audio mixing using Adobe Premiere Pro and DaVinci Resolve.', syllabus:['Timeline workflow and cuts','Color grading techniques','Audio mixing and sound design','Exporting for web, broadcast, and social'], tools:['Premiere Pro','DaVinci Resolve','After Effects','Audition'], outcomes:['Edit polished short films','Grade and color videos','Export for multiple platforms'] },
		{ id:'c13', image: IMAGES_BASE+'seo-marketing.jpg',    title:'SEO & Content Marketing Strategy',            category:'Business',       level:'Intermediate', duration:'6 Weeks',  price:'$109', instructor:'Mina Shrestha',   rating:'4.7', students:'1,890', overview:'Rank higher in search engines and drive organic traffic with on-page SEO, link building, and a content calendar.', syllabus:['Keyword research and SERP analysis','On-page and technical SEO','Content calendar and pillar strategy','Link building and authority building'], tools:['Ahrefs','SEMrush','Google Search Console','WordPress'], outcomes:['Improve organic search rankings','Build a content pipeline','Track and report SEO performance'] },
		{ id:'c14', image: IMAGES_BASE+'cloud-aws.jpg',        title:'Cloud Computing with AWS',                    category:'Cloud & DevOps', level:'Intermediate', duration:'8 Weeks',  price:'$169', instructor:'Binod Shrestha',  rating:'4.8', students:'1,540', overview:'Provision, manage, and secure cloud infrastructure on Amazon Web Services including EC2, S3, Lambda, and VPC.', syllabus:['AWS core services overview','EC2 instances and auto-scaling','S3 storage and CloudFront CDN','IAM roles, policies, and CloudWatch'], tools:['AWS Console','Terraform','CLI','CloudFormation'], outcomes:['Deploy scalable cloud apps','Configure secure IAM policies','Monitor and optimize cloud costs'] },
		{ id:'c15', image: IMAGES_BASE+'photography.jpg',      title:'Digital Photography Masterclass',             category:'Design',         level:'Beginner',     duration:'4 Weeks',  price:'$69',  instructor:'Samita Karki',    rating:'4.5', students:'990', overview:'Learn manual camera controls, composition rules, lighting techniques, and Lightroom post-processing.', syllabus:['Aperture, shutter speed, ISO triangle','Composition: rule of thirds, leading lines','Natural and artificial lighting setups','Lightroom editing workflow'], tools:['DSLR/Mirrorless','Lightroom','Photoshop','Snapseed'], outcomes:['Shoot in full manual mode','Edit photos professionally','Build a photo portfolio'] },
		{ id:'c16', image: IMAGES_BASE+'finance.jpg',          title:'Financial Literacy & Smart Investing',        category:'Business',       level:'Beginner',     duration:'5 Weeks',  price:'$79',  instructor:'Anil Maharjan',   rating:'4.6', students:'2,440', overview:'Understand personal finance, budgeting, stock market basics, mutual funds, and long-term wealth building strategies.', syllabus:['Budgeting and expense tracking','Emergency funds and insurance','Stock market and index investing','Retirement planning and compound interest'], tools:['Excel','Google Sheets','Trading View','Zerodha'], outcomes:['Create a personal budget plan','Start investing with low risk','Understand tax-efficient saving'] },
		{ id:'c17', image: IMAGES_BASE+'nodejs-backend.jpg',   title:'Node.js & Express Backend Development',       category:'Programming',    level:'Intermediate', duration:'7 Weeks',  price:'$119', instructor:'Kabir Lama',      rating:'4.7', students:'1,380', overview:'Build performant REST APIs, handle authentication, and connect Node.js apps to MongoDB and PostgreSQL databases.', syllabus:['Node.js runtime and event loop','Express routing and middleware','JWT authentication and authorization','MongoDB and PostgreSQL integration'], tools:['Node.js','Express','MongoDB','Postman'], outcomes:['Build production-grade APIs','Implement secure auth flows','Deploy Node apps to cloud'] },
		{ id:'c18', image: IMAGES_BASE+'ai-chatgpt.jpg',       title:'AI & ChatGPT for Productivity',               category:'Data Science',   level:'Beginner',     duration:'3 Weeks',  price:'$59',  instructor:'Pratima Gurung',  rating:'4.7', students:'3,600', overview:'Use AI tools including ChatGPT, Copilot, and Midjourney to automate tasks, generate content, and boost daily productivity.', syllabus:['Prompt engineering fundamentals','AI for writing and research','Image generation with Midjourney','Automation with Zapier and Make'], tools:['ChatGPT','Copilot','Midjourney','Zapier'], outcomes:['Write effective prompts','Automate repetitive workflows','Use AI for business tasks'] },
		{ id:'c19', image: IMAGES_BASE+'3d-modeling.jpg',      title:'3D Modeling & Animation with Blender',        category:'Design',         level:'Intermediate', duration:'8 Weeks',  price:'$109', instructor:'Saroj Basnet',    rating:'4.6', students:'870', overview:'Create characters, environments, and animated sequences in Blender from polygon modeling to final render.', syllabus:['Blender interface and navigation','Polygon modeling and sculpting','Rigging and basic animation','Lighting, materials, and final render'], tools:['Blender','Cycles Renderer','HDRI Haven','After Effects'], outcomes:['Model 3D assets for games or film','Rig and animate characters','Produce studio-quality renders'] },
		{ id:'c20', image: IMAGES_BASE+'project-mgmt.jpg',     title:'Project Management Professional (PMP Prep)',  category:'Business',       level:'Advanced',     duration:'7 Weeks',  price:'$149', instructor:'Sunita Thapa',    rating:'4.8', students:'2,080', overview:'Master agile, scrum, and waterfall project management methodologies required for the PMP certification exam.', syllabus:['Project initiation and scope definition','Agile and Scrum frameworks','Risk management and mitigation','Stakeholder communication and reporting'], tools:['Jira','MS Project','Confluence','Miro'], outcomes:['Prepare for PMP certification','Lead cross-functional teams','Deliver projects on time and budget'] },
		{ id:'c21', image: IMAGES_BASE+'excel-analytics.jpg',  title:'Excel & Business Data Analytics',             category:'Business',       level:'Beginner',     duration:'4 Weeks',  price:'$69',  instructor:'Bimala Upreti',   rating:'4.5', students:'3,280', overview:'Master Excel formulas, pivot tables, dashboards, and Power Query to analyze and report business data efficiently.', syllabus:['Core formulas: VLOOKUP, INDEX-MATCH, IF','PivotTables and PivotCharts','Power Query data transformation','Dashboard design best practices'], tools:['Excel','Power Query','Power Pivot','Power BI'], outcomes:['Automate reporting workflows','Build dynamic dashboards','Clean and transform raw data'] },
		{ id:'c22', image: IMAGES_BASE+'copywriting.jpg',      title:'Copywriting & Content Creation',              category:'Business',       level:'Beginner',     duration:'4 Weeks',  price:'$69',  instructor:'Smriti Panta',    rating:'4.6', students:'1,710', overview:'Write persuasive copy for websites, emails, ads, and social media using proven frameworks like AIDA and PAS.', syllabus:['AIDA and PAS copywriting frameworks','Headline and hook writing','Email sequences and nurture campaigns','Social media content calendars'], tools:['Grammarly','Notion','Canva','Mailchimp'], outcomes:['Write high-converting landing pages','Build email sequences','Create a consistent brand voice'] },
		{ id:'c23', image: IMAGES_BASE+'devops.jpg',           title:'DevOps with Docker & Kubernetes',             category:'Cloud & DevOps', level:'Advanced',     duration:'9 Weeks',  price:'$179', instructor:'Roshan Karmacharya', rating:'4.8', students:'1,200', overview:'Containerize applications with Docker, orchestrate with Kubernetes, and set up CI/CD pipelines for automated delivery.', syllabus:['Docker containers and image building','Docker Compose multi-service apps','Kubernetes pods, services, and deployments','CI/CD with GitHub Actions'], tools:['Docker','Kubernetes','GitHub Actions','Helm'], outcomes:['Containerize and deploy any app','Manage K8s clusters','Build automated CI/CD pipelines'] },
		{ id:'c24', image: IMAGES_BASE+'java.jpg',             title:'Java Programming Mastery',                    category:'Programming',    level:'Intermediate', duration:'9 Weeks',  price:'$129', instructor:'Rajesh Joshi',    rating:'4.6', students:'1,640', overview:'Learn core and advanced Java including OOP, collections, multithreading, Spring Boot, and REST API development.', syllabus:['Java OOP: classes, inheritance, polymorphism','Collections framework and generics','Multithreading and concurrency','Spring Boot REST API development'], tools:['IntelliJ IDEA','Maven','Spring Boot','Postman'], outcomes:['Build enterprise Java applications','Design RESTful APIs with Spring Boot','Write multithreaded programs'] },
		{ id:'c25', image: IMAGES_BASE+'social-media.jpg',     title:'Social Media Marketing & Growth Strategy',    category:'Business',       level:'Beginner',     duration:'5 Weeks',  price:'$89',  instructor:'Preeti Manandhar', rating:'4.5', students:'2,970', overview:'Grow brand awareness, engagement, and leads across Instagram, TikTok, LinkedIn, and YouTube with proven strategies.', syllabus:['Platform algorithms and organic growth tactics','Content pillars and posting cadence','Paid social ads (Meta & LinkedIn)','Analytics and reporting'], tools:['Meta Business Suite','Buffer','Canva','Hootsuite'], outcomes:['Grow an organic following','Run profitable social ads','Track social ROI'] },
		{ id:'c26', image: IMAGES_BASE+'film-production.jpg',  title:'Film Production & Visual Storytelling',       category:'Design',         level:'Intermediate', duration:'7 Weeks',  price:'$119', instructor:'Bikash Maharjan', rating:'4.6', students:'760', overview:'Direct, shoot, and edit short films and documentaries using professional cinematography and storytelling techniques.', syllabus:['Script and storyboard development','Camera movement and shot composition','Location lighting and sound recording','Editing and sound design in Premiere'], tools:['Premiere Pro','DaVinci Resolve','Logic Pro','DSLR/Cinema Camera'], outcomes:['Produce a short film end-to-end','Apply cinematic storytelling techniques','Build a director\'s portfolio'] },
		{ id:'c27', image: IMAGES_BASE+'flutter.jpg',          title:'Flutter Cross-Platform App Development',      category:'Programming',    level:'Advanced',     duration:'10 Weeks', price:'$159', instructor:'Bibek Tamang',    rating:'4.7', students:'1,050', overview:'Build beautiful, high-performance mobile and web apps for iOS, Android, and web from a single Dart codebase.', syllabus:['Dart language fundamentals','Flutter widgets and layouts','State management with Riverpod/Bloc','Firebase integration and deployment'], tools:['Flutter','Dart','Firebase','Android Studio'], outcomes:['Ship apps to iOS and Android simultaneously','Implement robust state management','Integrate cloud backends with Firebase'] },
		{ id:'c28', image: IMAGES_BASE+'blockchain.jpg',       title:'Blockchain & Web3 Development',               category:'Programming',    level:'Advanced',     duration:'9 Weeks',  price:'$189', instructor:'Nabin Giri',      rating:'4.7', students:'820', overview:'Build decentralized applications using Ethereum, Solidity smart contracts, and popular Web3 libraries.', syllabus:['Blockchain architecture and consensus','Solidity smart contract development','Web3.js and ethers.js integration','DeFi protocols and NFT standards'], tools:['Solidity','Hardhat','MetaMask','Ethers.js'], outcomes:['Deploy smart contracts to Ethereum testnet','Build a full dApp','Understand DeFi and NFT ecosystems'] },
		{ id:'c29', image: IMAGES_BASE+'public-speaking.jpg',  title:'Public Speaking & Communication Mastery',     category:'Business',       level:'Beginner',     duration:'4 Weeks',  price:'$59',  instructor:'Geeta Bhattarai', rating:'4.5', students:'1,930', overview:'Overcome speaking anxiety, structure compelling presentations, and deliver with confidence in any professional setting.', syllabus:['Managing nervousness and building confidence','Storytelling frameworks (Hero\'s Journey, STAR)','Slide design and visual storytelling','Q&A handling and panel discussions'], tools:['PowerPoint','Canva','Zoom','Loom'], outcomes:['Speak confidently in meetings and events','Design compelling presentations','Handle tough audience questions'] },
		{ id:'c30', image: IMAGES_BASE+'ethical-hacking.jpg',  title:'Ethical Hacking & Penetration Testing',       category:'Cybersecurity',  level:'Advanced',     duration:'10 Weeks', price:'$199', instructor:'Sagar Bhusal',    rating:'4.9', students:'1,340', overview:'Learn offensive security skills including reconnaissance, exploitation, privilege escalation, and reporting to help organizations stay secure.', syllabus:['Pentest methodology and scoping','Network scanning and enumeration','Web application attacks: SQLi, XSS, CSRF','Post-exploitation and reporting'], tools:['Kali Linux','Metasploit','Burp Suite','Nmap'], outcomes:['Conduct authorized penetration tests','Write professional pentest reports','Earn pathways to CEH/OSCP certs'] },
		{ id:'c31', image: IMAGES_BASE+'html-css-free.jpg',     title:'HTML & CSS for Complete Beginners',            category:'Programming',    level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Aaditya Sharma',  rating:'4.8', students:'5,420', overview:'Build your first webpage from scratch using clean, semantic HTML5 and modern CSS — no prior experience needed.',                                             syllabus:['HTML structure and semantic tags','CSS selectors and the box model','Flexbox and responsive layouts','Publishing your first webpage'], tools:['VS Code','Chrome DevTools','GitHub Pages','CodePen'], outcomes:['Build a personal webpage from scratch','Understand semantic HTML','Apply responsive CSS layouts'] },
		{ id:'c32', image: IMAGES_BASE+'intro-datascience.jpg', title:'Introduction to Data Science',                 category:'Data Science',   level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Nisha Koirala',   rating:'4.7', students:'3,890', overview:'Explore what data science is, how it is used across industries, and get hands-on with basic Python data analysis.',                                         syllabus:['What is data science and who uses it','Python basics for data exploration','Reading and summarizing datasets with Pandas','Your first data visualization'], tools:['Python','Jupyter Notebook','Pandas','Matplotlib'], outcomes:['Understand the data science workflow','Explore real datasets with Python','Create basic charts and summaries'] },
		{ id:'c33', image: IMAGES_BASE+'entrepreneurship.jpg',  title:'Entrepreneurship Fundamentals',                category:'Business',       level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Ritika Adhikari', rating:'4.6', students:'2,760', overview:'Learn how ideas become businesses — from identifying opportunities and validating products to writing a lean business plan.',                               syllabus:['Identifying market problems and opportunities','Customer discovery and lean validation','Business model canvas','Pitching your idea clearly'], tools:['Notion','Google Forms','Canva','Lean Canvas'], outcomes:['Validate a startup idea','Build a lean business model','Pitch confidently to stakeholders'] },
		{ id:'c34', image: IMAGES_BASE+'design-thinking.jpg',   title:'Design Thinking Fundamentals',                 category:'Design',         level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Karan Basnet',    rating:'4.7', students:'2,100', overview:'Apply the five-stage design thinking process — empathise, define, ideate, prototype, and test — to solve real-world problems.',                           syllabus:['Empathy mapping and user research','Problem definition and insight framing','Ideation and brainstorming techniques','Paper prototyping and usability testing'], tools:['FigJam','Notion','Miro','Sticky Notes'], outcomes:['Run a design thinking sprint','Create empathy maps','Build and test a paper prototype'] },
		{ id:'c35', image: IMAGES_BASE+'cyber-safety.jpg',      title:'Cyber Safety & Privacy Basics',                category:'Cybersecurity',  level:'Beginner',     duration:'1 Week',   price:'Free', instructor:'Deepak Rana',     rating:'4.5', students:'4,670', overview:'Protect yourself online with practical knowledge about phishing, strong passwords, two-factor authentication, and safe browsing habits.',                   syllabus:['Understanding common online threats','Creating and managing strong passwords','Setting up two-factor authentication','Spotting phishing emails and scams'], tools:['Bitwarden','Google Authenticator','Have I Been Pwned','Privacy Badger'], outcomes:['Identify and avoid common cyber threats','Set up strong password hygiene','Protect personal data online'] },
		{ id:'c36', image: IMAGES_BASE+'git-github.jpg',        title:'Git & GitHub for Beginners',                   category:'Programming',    level:'Beginner',     duration:'1 Week',   price:'Free', instructor:'Saurav Pandey',   rating:'4.8', students:'6,200', overview:'Learn version control with Git and how to collaborate on code using GitHub — an essential skill for every developer.',                                     syllabus:['Git init, add, commit, log','Branching and merging','Pull requests and code review on GitHub','Resolving merge conflicts'], tools:['Git','GitHub','VS Code','GitHub Desktop'], outcomes:['Manage code with Git version control','Collaborate via GitHub pull requests','Resolve merge conflicts confidently'] },
		{ id:'c37', image: IMAGES_BASE+'intro-cloud.jpg',       title:'Introduction to Cloud Computing',              category:'Cloud & DevOps', level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Binod Shrestha',  rating:'4.6', students:'3,010', overview:'Understand cloud concepts, service models (IaaS, PaaS, SaaS), and major providers to start your cloud career path.',                                       syllabus:['What is cloud computing and why it matters','IaaS vs PaaS vs SaaS explained','Overview of AWS, Azure, and GCP','Cloud pricing, security, and compliance basics'], tools:['AWS Free Tier','Azure Free Account','Google Cloud Console','draw.io'], outcomes:['Explain core cloud concepts confidently','Compare AWS, Azure, and GCP','Set up a free cloud account and launch a service'] },
		{ id:'c38', image: IMAGES_BASE+'communication.jpg',     title:'Professional Communication Skills',            category:'Business',       level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Geeta Bhattarai', rating:'4.5', students:'3,880', overview:'Build workplace communication skills — writing clear emails, running effective meetings, and presenting ideas with confidence.',                             syllabus:['Writing professional emails and messages','Active listening and giving feedback','Running productive meetings','Presenting ideas clearly and concisely'], tools:['Gmail / Outlook','Notion','Zoom','Google Slides'], outcomes:['Write clear, professional emails','Facilitate effective team meetings','Present ideas with confidence'] },
		{ id:'c39', image: IMAGES_BASE+'statistics.jpg',        title:'Statistics for Data Science',                  category:'Data Science',   level:'Beginner',     duration:'3 Weeks',  price:'Free', instructor:'Priya Dhakal',    rating:'4.7', students:'2,540', overview:'Master the statistical foundations every data scientist needs — mean, median, distributions, correlation, and hypothesis testing.',                         syllabus:['Descriptive statistics: mean, median, mode','Probability and distributions','Correlation and regression basics','Hypothesis testing and p-values'], tools:['Python','NumPy','SciPy','Jupyter Notebook'], outcomes:['Interpret descriptive and inferential statistics','Apply probability to real datasets','Understand hypothesis testing results'] },
		{ id:'c40', image: IMAGES_BASE+'motion-graphics.jpg',   title:'Introduction to Motion Graphics',              category:'Design',         level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Anita Thapa',     rating:'4.6', students:'1,890', overview:'Get started with motion design — learn keyframes, easing, and transitions to bring graphics to life in Adobe After Effects.',                               syllabus:['After Effects interface and workspace','Keyframes, timing, and easing','Text animations and shape layers','Exporting for social media and web'], tools:['Adobe After Effects','Adobe Illustrator','Media Encoder','Behance'], outcomes:['Create smooth animated graphics','Apply easing to keyframe animations','Export motion graphics for digital use'] }
	];

	const searchInput = document.getElementById('searchCourse');
	const levelFilter = document.getElementById('levelFilter');
	const categoryFilters = document.getElementById('categoryFilters');
	const instructorDropdown = document.getElementById('instructorDropdown');
	const instructorToggle = document.getElementById('instructorToggle');
	const instructorMenu = document.getElementById('instructorMenu');
	const instructorSearch = document.getElementById('instructorSearch');
	const instructorOptions = document.getElementById('instructorOptions');
	const instructorSelectedLabel = document.getElementById('instructorSelectedLabel');
	const priceRangeFilter = document.getElementById('priceRangeFilter');
	const resetFilters = document.getElementById('resetFilters');
	const coursesGrid = document.getElementById('coursesGrid');
	const courseCount = document.getElementById('courseCount');

	let currentCategory = 'all';
	let currentCourseId = null;
	let currentInstructor = 'all';

	function parseCoursePrice(priceText) {
		return parseInt(String(priceText).replace(/[^0-9]/g, ''), 10) || 0;
	}

	function getSelectedBudgetRange() {
		if (priceRangeFilter.value === 'all') {
			return { min: 0, max: Number.MAX_SAFE_INTEGER };
		}
		if (priceRangeFilter.value === '0-0') {
			return { min: 0, max: 0 };
		}
		const parts = priceRangeFilter.value.split('-');
		return {
			min: parseInt(parts[0], 10),
			max: parseInt(parts[1], 10)
		};
	}

	function getUniqueInstructors() {
		return Array.from(new Set(courses.map(function (course) {
			return course.instructor;
		}))).sort();
	}

	function closeInstructorDropdown() {
		instructorDropdown.classList.remove('open');
		instructorMenu.hidden = true;
		instructorToggle.setAttribute('aria-expanded', 'false');
	}

	function openInstructorDropdown() {
		instructorDropdown.classList.add('open');
		instructorMenu.hidden = false;
		instructorToggle.setAttribute('aria-expanded', 'true');
	}

	function setInstructor(value) {
		currentInstructor = value;
		instructorSelectedLabel.textContent = value === 'all' ? 'All Instructors' : value;
		rerender();
	}

	function renderInstructorOptions(query) {
		const keyword = (query || '').trim().toLowerCase();
		const names = getUniqueInstructors().filter(function (name) {
			return !keyword || name.toLowerCase().includes(keyword);
		});

		instructorOptions.innerHTML = '';

		if (!keyword || 'all instructors'.includes(keyword)) {
			const allBtn = document.createElement('button');
			allBtn.type = 'button';
			allBtn.className = 'instructor-option' + (currentInstructor === 'all' ? ' active' : '');
			allBtn.setAttribute('data-value', 'all');
			allBtn.textContent = 'All Instructors';
			instructorOptions.appendChild(allBtn);
		}

		if (!names.length) {
			const empty = document.createElement('div');
			empty.className = 'instructor-empty';
			empty.textContent = 'No instructor found.';
			instructorOptions.appendChild(empty);
			return;
		}

		names.forEach(function (name) {
			const btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'instructor-option' + (currentInstructor === name ? ' active' : '');
			btn.setAttribute('data-value', name);
			btn.textContent = name;
			instructorOptions.appendChild(btn);
		});
	}

	function renderCards(filteredCourses) {
		coursesGrid.innerHTML = '';
		courseCount.textContent = filteredCourses.length + (filteredCourses.length === 1 ? ' course' : ' courses');

		if (!filteredCourses.length) {
			coursesGrid.innerHTML = '<div class="empty-state">No courses match these filters. Try a different category or reset all filters.</div>';
			return;
		}

		filteredCourses.forEach(function (course) {
			const card = document.createElement('article');
			card.className = 'course-card' + (course.id === currentCourseId ? ' active' : '');
			card.setAttribute('data-id', course.id);
			card.innerHTML =
				'<div class="course-thumb" style="background-image:url(\'' + course.image + '\')"></div>' +
				'<div class="course-card-content">' +
					'<div class="course-meta-top">' +
						'<span class="course-category">' + course.category + '</span>' +
						'<span class="course-level">' + course.level + '</span>' +
					'</div>' +
					'<h3 class="course-title">' + course.title + '</h3>' +
					'<p class="course-instructor">By ' + course.instructor + '</p>' +
					'<div class="course-rating">' +
						'<span class="stars">&#9733;</span>' +
						'<span class="rating-num">' + course.rating + '</span>' +
						'<span class="rating-students">(' + course.students + ' students)</span>' +
					'</div>' +
					'<div class="course-meta-bottom">' +
						'<span class="course-duration"><i class="bi bi-clock"></i> ' + course.duration + '</span>' +
						'<span class="course-price">' + course.price + '</span>' +
					'</div>' +
				'</div>';

			card.addEventListener('click', function () {
				currentCourseId = course.id;
				renderCards(getFilteredCourses());
			});

			coursesGrid.appendChild(card);
		});

		if (!currentCourseId) {
			currentCourseId = filteredCourses[0].id;
			renderCards(filteredCourses);
		}
	}

	function getFilteredCourses() {
		const keyword = (searchInput.value || '').trim().toLowerCase();
		const selectedLevel = levelFilter.value;
		const budgetRange = getSelectedBudgetRange();

		return courses.filter(function (course) {
			const byCategory = currentCategory === 'all' || course.category === currentCategory;
			const byLevel = selectedLevel === 'all' || course.level === selectedLevel;
			const byInstructor = currentInstructor === 'all' || course.instructor === currentInstructor;
			const priceNum = parseCoursePrice(course.price);
			const byPrice = priceNum >= budgetRange.min && priceNum <= budgetRange.max;
			const byKeyword = !keyword ||
				course.title.toLowerCase().includes(keyword) ||
				course.category.toLowerCase().includes(keyword) ||
				course.instructor.toLowerCase().includes(keyword) ||
				course.tools.join(' ').toLowerCase().includes(keyword);

			return byCategory && byLevel && byInstructor && byPrice && byKeyword;
		});
	}

	function rerender() {
		const filtered = getFilteredCourses();
		if (!filtered.some(function (course) { return course.id === currentCourseId; })) {
			currentCourseId = filtered.length ? filtered[0].id : null;
		}
		renderCards(filtered);
	}

	categoryFilters.addEventListener('click', function (event) {
		const target = event.target;
		if (!target.classList.contains('filter-chip')) return;

		currentCategory = target.getAttribute('data-filter');
		Array.prototype.forEach.call(categoryFilters.querySelectorAll('.filter-chip'), function (chip) {
			chip.classList.remove('active');
		});
		target.classList.add('active');
		rerender();
	});

	instructorToggle.addEventListener('click', function () {
		if (instructorDropdown.classList.contains('open')) {
			closeInstructorDropdown();
			return;
		}
		renderInstructorOptions(instructorSearch.value);
		openInstructorDropdown();
		instructorSearch.focus();
	});

	instructorSearch.addEventListener('input', function () {
		renderInstructorOptions(instructorSearch.value);
	});

	instructorOptions.addEventListener('click', function (event) {
		const target = event.target;
		if (!target.classList.contains('instructor-option')) return;
		setInstructor(target.getAttribute('data-value'));
		instructorSearch.value = '';
		renderInstructorOptions('');
		closeInstructorDropdown();
	});

	document.addEventListener('click', function (event) {
		if (!instructorDropdown.contains(event.target)) {
			closeInstructorDropdown();
		}
	});

	instructorSearch.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeInstructorDropdown();
			instructorToggle.focus();
		}
	});

	[searchInput, levelFilter, priceRangeFilter].forEach(function (element) {
		element.addEventListener('input', rerender);
		element.addEventListener('change', rerender);
	});

	resetFilters.addEventListener('click', function () {
		currentCategory = 'all';
		currentCourseId = null;
		searchInput.value = '';
		levelFilter.value = 'all';
		currentInstructor = 'all';
		instructorSelectedLabel.textContent = 'All Instructors';
		instructorSearch.value = '';
		renderInstructorOptions('');
		closeInstructorDropdown();
		priceRangeFilter.value = 'all';
		Array.prototype.forEach.call(categoryFilters.querySelectorAll('.filter-chip'), function (chip) {
			chip.classList.toggle('active', chip.getAttribute('data-filter') === 'all');
		});
		rerender();
	});

	renderInstructorOptions('');
	rerender();
})();
