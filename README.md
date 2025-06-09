## **University Online Election System**

The ***University Online Election System*** is a web-based application developed to facilitate the management and execution of student elections within ***Philippine Christian University – Dasmariñas***. It aims to provide a **secure**, **transparent**, and **user-friendly** platform for handling election-related data, such as candidates, election articles, and voter participation.

The **admin panel** allows authorized personnel to **create**, **view**, **edit**, and **delete** candidates and election articles. It also supports **rich text formatting** and **image embedding** for articles through **Quill.js**, enabling the publication of informative and visually engaging election content.

**Role-based access control** ensures that only authenticated admins can access the election management features. The system is designed to help **streamline** the election process, **reduce manual errors**, and promote better student engagement through accessible and up-to-date election information.

This project was developed as part of the **internship program** to support the digital transformation of university processes by providing a **reliable** and **efficient** platform for student elections.


---

## **Technologies Used**

This project uses the following technologies and tools to build a reliable and efficient online election system for ***Philippine Christian University – Dasmariñas***:

**Frontend**

* **Tailwind CSS** – A utility-first CSS framework that helps build custom and responsive user interfaces quickly and consistently.
  [https://tailwindcss.com/](https://tailwindcss.com/)

**Backend**

* **PHP 8.2+** – The programming language used for backend logic, offering modern features and good performance.
  [https://www.php.net/](https://www.php.net/)
* **Laravel 12** – A PHP framework that provides clean and simple syntax for creating scalable web applications.
  [https://laravel.com/](https://laravel.com/)
* **Intervention Image** – A PHP package for image handling and manipulation.
  [http://image.intervention.io/](http://image.intervention.io/)
* **Maatwebsite Excel** – A package for handling Excel file imports and exports.
  [https://maatwebsite.nl/](https://maatwebsite.nl/)
* **Mews Purifier** – A package to clean HTML inputs for security.
  [https://github.com/mewebstudio/Purifier](https://github.com/mewebstudio/Purifier)

**Database**

* **MySQL** – The relational database management system used to store application data such as users, candidates, and election articles.
  [https://www.mysql.com/](https://www.mysql.com/)

**Tools and Platforms**

* **Visual Studio Code** – Code editor used for writing and managing code.
  [https://code.visualstudio.com/](https://code.visualstudio.com/)
* **Laragon** – Local development environment for running PHP and MySQL easily.
  [https://laragon.org/](https://laragon.org/)
* **Composer** – PHP dependency manager to manage libraries and packages.
  [https://getcomposer.org/](https://getcomposer.org/)
* **NPM & Vite** – Node.js package manager and build tool used to manage JavaScript dependencies and build frontend assets.
  [https://www.npmjs.com/](https://www.npmjs.com/) | [https://vitejs.dev/](https://vitejs.dev/)
* **Git & GitHub** – Version control system and platform used to manage source code and collaborate.
  [https://git-scm.com/](https://git-scm.com/) | [https://github.com/](https://github.com/)


---

## 🔧 **Installation and Setup**

Follow these steps to set up the ***University Online Election System*** on your local development environment. This was developed for **Philippine Christian University – Dasmariñas** as part of an internship project.


### 📌 **Prerequisites**

Make sure the following tools are installed and properly set up:

* **PHP 8.2 or higher**
* **Composer**
* **Node.js and NPM**
* **MySQL**
* **Laragon** *or* **XAMPP** *(for local server setup)*

  > ✅ **Laragon** is recommended for Windows users because it is more stable, lightweight, and doesn’t require placing the project inside `htdocs`.
  > ⚠️ **XAMPP** can also be used, but not recommended due to possible file corruption issues and limitations with Laravel projects. Use with caution.
* **Git** *(optional, needed only if cloning from a GitHub repository)*


🛠️ **Setup Steps**

1. **Download the project files** from the **Google Drive link** provided by the developer.
   After downloading, extract the `.zip` file to any folder of your choice
   *(e.g., `C:\Users\YourName\Desktop\Election System\ElectionSystem`)*

   > 💡 *No need to place this inside `htdocs` if you're using Laragon.*

2. **Open the extracted folder in Visual Studio Code**:

   * Right-click the folder → **Open with Code**, or
   * Open VS Code → **File > Open Folder...**

3. **Open the terminal in VS Code**:

   * Go to **Terminal > New Terminal** or press **\`Ctrl + \`\`**
   * Click the **dropdown arrow** beside the `+` sign in the terminal tab
   * Select **Command Prompt** instead of PowerShell
     *(PowerShell sometimes causes command issues like with `npm run dev`)*

4. **Install PHP dependencies**:

   ```bash
   composer install
   ```

5. **Install JavaScript dependencies**:

   ```bash
   npm install
   ```

6. **Create your `.env` file**:

   * Duplicate `.env.example` and rename to `.env`
   * Open `.env` and configure your:

     * `DB_DATABASE`
     * `DB_USERNAME`
     * `DB_PASSWORD`
       based on your local MySQL setup (usually set via Laragon or XAMPP)

7. **Generate the application key**:

   ```bash
   php artisan key:generate
   ```

8. **Run database migrations and seeders**:

   ```bash
   php artisan migrate --seed
   ```

   > This creates your tables and inserts sample data for testing

9. **Build frontend assets** (for production-ready assets):

   ```bash
   npm run build
   ```

10. **For real-time development**, run the dev watcher:

```bash
npm run dev
```

> * This should run in a **separate tab** in the VS Code terminal using Command Prompt *

11. **Start the Laravel development server**:

```bash
php artisan serve
```

> * Also run this in a **separate Command Prompt tab** *

12. **Open the application in your browser**:  
    Visit: [http://localhost:8000](http://localhost:8000)  
    You should see the **University Online Election System** homepage or login screen.

    *If everything is set up correctly, you're good to go! Otherwise, double-check your `.env` file and running services.*


---

### ✅ **Finished Modules**

During the development of the **University Online Election System**, the following modules have been successfully implemented. These are mostly focused on admin-side functionalities, as the team prioritized administrative features during development. Please note that while these modules are functional, some features (especially on the user side) are still under development and may contain bugs or need refinement.


#### **1. Manage Candidates**

* Admin can add new candidates with relevant information (e.g., name, position, course, year level).
* Supports:

  * Search functionality (by name, course, or active/inactive status)
  * View candidate profiles
  * Edit candidate details
  * Delete candidates
  * Pagination for improved readability and performance


#### **2. Manage Positions**

* Allows admin to define positions for the election (e.g., President, Vice President, Secretary).
* Supports:

  * Add, edit, and delete positions
  * Search functionality


#### **3. Manage Election Articles**

* Admin can manage informational articles related to elections (guidelines, rules, updates).
* Supports:

  * Add, view, edit, and delete articles


#### **4. Verify Voters**

* Enables admin to manage the list of verified student voters.
* Supports:

  * Importing verified voters via `.xlsx` files
  * Exporting list of verified voters
  * Searching and filtering by name
  * Filtering by Academic Year and Semester
  * Removing specific verified voters
  * Pagination for better navigation



### ⚠️ **Pending or Partial Features**

The following features are proposed and partially implemented but not yet fully functional:

* **Student Login via QR Code (Student Number)**

  > The system is intended to allow login using a QR code of the student number for faster access. However, this functionality is not yet working. Currently, login is done manually by entering credentials.

* **Login Error Handling**

  > Incorrect password input does not yet show an alert or feedback. Proper validation messages are still in progress.

* **Student Registration**

  > Student self-registration is disabled. All focus was initially placed on admin-side modules.

---

## 🛠️ How to Use the University Online Election System

### 👑 **Super Admin**

Super Admins have full access to all administrative settings and election configurations.

#### ✅ What You Can Do:

1. **Login**

   * Access the system via the `/admin` path using your Super Admin credentials.

2. **User Management**

   * Add, edit, or remove Admins, Moderators, and Voters (Students).
   * Assign roles such as Admin, Moderator, or User.

3. **Manage Admin Roles**

   * Create, update, and delete roles (e.g., Admin, Moderator).
   * Each role has its own set of permissions.

4. **Audit Logs**

   * Track user activity, including logins and actions performed.
   * View timestamped records like:

     ```
     Timestamp         User        Action       Details
     2023-10-01 12:34  admin1      Login        User admin1 logged in
     ```

5. **System Settings**

   * Set the current Academic Year and Semester.
   * Manage courses (add/edit/delete).
   * Update system-wide configurations and defaults.

6. **Verify Voters**

   * Import verified student voters via `.xlsx`.
   * Filter by Academic Year and Semester.
   * Remove, search, or export verified users.

7. **Profile Settings**

   * Update name, email, ID number, profile picture, password, course, and role.



### 🧑‍💼 **Admin (Elevated Student Account)**

Admins are users with elevated access (typically students assigned as officers or moderators) to help manage the election operations.

#### ✅ What You Can Do:

1. **Login**

   * Use your admin credentials at `/admin`.

2. **Election Management**

   * **Manage Elections** – Create, edit, or delete elections.
   * **Manage Candidates** – Add/edit/delete candidate profiles; search candidates by name, course, or status.
   * **Manage Positions** – Set available positions (e.g., President, VP); add/edit/delete roles.
   * **Manage Election Articles** – Post or update important election-related content.
   * **Manage Election Party List** – Create, edit, view, or delete party names.

3. **Vote Monitoring**

   * **Tally Votes** – Real-time viewing of vote counts by position.
   * **Election Summary List** – See candidates, votes received, and vote percentage.
   * **View Election Results & Polls** – Check live and final results.

4. **Voter Handling**

   * View and manage voters: reset passwords, allow registration, filter by election.
   * Assign voters to specific elections via "Register Election" module.

5. **Reports**

   * Create, track, and export system reports based on election activity or incidents.



### 🎓 **Voter / Student**

Voters (Students) have access to a simplified interface for viewing elections, casting votes, and tracking their registration.

#### ✅ What You Can Do:

1. **Login**

   * Use your student number or email and password at the `/login` page.

2. **Browse Candidates**

   * View full candidate profiles, platforms, and party affiliations.

3. **Register for an Election**

   * Go to the "Register Election" page and select the appropriate election from a dropdown.
   * Registration is only open for allowed elections based on your course, year level, or semester.

4. **Cast Your Vote**

   * Access the "Cast Vote" module during election periods.
   * Instructions:

     * Vote for one candidate per position (President, VP, etc.).
     * You may vote for multiple representatives, depending on the configuration.
     * Review your selections before submitting.

5. **Track Election Activity**

   * **Elections** – View details of current and past elections, including your registration history.
   * **Results** – View published results per position.
   * **Tally** – View vote count breakdowns by position or party.
   * **Contacts** – Access support or system admin contacts for assistance.



### ⚠️ Important Notes

* Both **Super Admin** and **Admin** access the system via `/admin`.
* **Roles and permissions** are managed through the **Admin Roles** module.
* The **QR code login** feature for students is still under development. Currently, manual login is required.
* Registration validation alerts (e.g., wrong password) are still being improved.
* Most **user-side functions** (like registration and casting votes) are only available **after voter verification**.
* **Recommended browser**: Google Chrome or Firefox for full feature support.


---

## 📝 Handover Note for Future Developers

Hello, Future Developer(s)!

Thanks for taking over the development or maintenance of the **University Online Election System**. To help you transition smoothly, here are some important notes based on the current state of the system as of handover:



### 🔐 Admin Access

* You can access the admin panel by visiting the `/admin` URL path.
* Only verified **Admins** and **Super Admins** can log in.
* Role assignments (e.g., Admin, Moderator, User) are managed in the **User Management** and **Admin Roles** modules.



### 👥 User Roles

* **Super Admin**: Has full access to system configurations (e.g., managing roles, audit logs, settings, verifying voters, etc.).
* **Admin**: Typically student officers or moderators with privileges to manage elections, candidates, positions, articles, and view results.
* **Voters/Students**: Regular users who can register for elections, browse candidates, and vote (after being verified).

> ⚠️ Be careful when changing role permissions — this may affect access levels across the system.



### 📥 Verified Voters/Students Import

* To import verified voters, use the Excel template with the following column headers:

  ```
  Name, ID Number, Academic Year, Semester
  ```
* **Strict formatting** is required. Import will fail if the structure is incorrect.
* The import feature includes error validation. Always review the error messages carefully before finalizing the import.



### 📅 Election and Eligibility Settings

* Events (elections) are filtered by:

  * **Academic Year**
  * **Semester**
  * **Program**
  * **Year Level**

> Students will **only see and register** for elections they're eligible for. This logic is already implemented.



### 🧪 Seeder & Sample Data

* Check `DatabaseSeeder.php` to see predefined entries like:

  * Sample elections
  * Sample positions and parties
  * Test admin/student users
* If you're using seeders for testing, **always update the event dates** to avoid empty tables or missing data on the frontend.



### 💡 Tools Used for Assistance

During development, the following AI tools were used to assist with code writing, debugging, and conceptual understanding:

* **ChatGPT (OpenAI)**
* **Claude (Anthropic)**
* **Bolt (by Zapier)**

Feel free to use these or other tools, but make sure to fully understand any code you're adding or modifying.



### 🔄 Version Control

If you're working on future updates, using **Git and GitHub** is highly encouraged:

* Track your progress and changes
* Collaborate efficiently with other devs
* Avoid overwriting or breaking key system features


### ✅ Final Notes

* Always test new features thoroughly, especially those involving logic for voter registration, election setup, and access control.
* The system uses **Laravel + Blade + Bootstrap + jQuery**, so a solid understanding of these technologies is helpful.
* For frontend modals and AJAX-based actions (e.g., edit/view/create), you’ll find modal code separated in partials for maintainability.


---

## 📁 Directory Tips

Here are some important directories and files in this Laravel project that you should be familiar with:

### 🔹 `app\Http\Controllers\`

* Contains all your **controller logic** for handling requests and passing data to views.
* Example: `CandidateController.php`, `ElectionArticleController.php`.

### 🔹 `app\Models\`

* Contains **Eloquent models** that represent your database tables.

### 🔹 `app\Imports\` and `app\Exports\`

* Used for **importing and exporting** data using Laravel Excel.
* Be mindful of the required CSV or XLSX format when importing.

### 🔹 `resources\views\`

* Holds all the **Blade view templates** for both the admin and voter sides.

  * `views\admin\` – Admin pages like managing candidates and articles.
  * `views\voter\` – Voter-facing pages such as registration and voting.
  * `views\components\` – Reusable Blade components like pagination and search box.
  * `views\layouts\` – Layout templates for consistent page structure.

### 🔹 `resources\js\` and `resources\css\`

* Custom JavaScript and CSS used in the frontend.

  * `resources\js\admin\` – Admin-specific scripts.
  * `resources\js\components\` – Reusable JS components.
  * `resources\js\app.js` and `bootstrap.js` – Entry points for JS logic.

### 🔹 `public\images\`, `public\storage\`, `public\build\assets\`

* Stores **public assets** like uploaded files, event posters, compiled CSS/JS, etc.
* Files here are **directly accessible via the browser**.

### 🔹 `database\seeders\` and `database\migrations\`

* Migrations define your **database schema**.
* Seeders help you preload sample data for testing.

---

## 🗂️ Important Files

### 📄 `composer.json` / `composer.lock`

* Define and lock your **PHP package dependencies** (like Laravel and Laravel Excel).

### 📄 `package.json` / `package-lock.json`

* Define and lock **JavaScript dependencies** used in development (e.g., Vite, Bootstrap, jQuery).

### 📄 `vite.config.js`

* Vite’s config file that manages how JS/CSS is compiled and served.

### 📄 `.env`

* Contains **environment variables** like DB credentials.
* Be careful not to expose this file publicly.

---

## ✅ Notes & Reminders

* When importing students, make sure your Excel/CSV follows this format:
  **Name, ID Number, Academic Year, Semester**
* Only students matching the criteria (year level, semester, academic year, and program) can register for specific events.
* QR Code and Mail setup are not yet implemented—future devs may add them.
* Code is modular—modals are separated into their own files where applicable for clarity.
* Keep JavaScript scoped properly to avoid conflicts, especially when using jQuery in modals or paginated views.


This system was built using Laravel, Blade, jQuery, and Bootstrap, with Excel import/export and pagination/search UI components.

Good luck and code with care! 

---

## 💌 Acknowledgment

I’m truly thankful to **Philippine Christian University - Dasmariñas** for giving me the chance to complete my internship here. This experience helped me apply what I’ve learned in school, improve my web development skills, and understand what it’s like to work on real projects.

I also want to sincerely thank **Ms. Diana Joy A. Virtudazo** and **Ms. Hannah Grace P. Baysa** for all the guidance and support they gave throughout my stay. Your patience and encouragement really meant a lot and helped me keep going.

This internship has been a great opportunity for growth, and I’ll always be grateful for everything I learned along the way.

---

## 👩‍💻 Developer Information

**Ma. Jane Therese L. Ricafort**  
Bachelor of Science in Information Technology  
Intern from Cavite State University - Silang Campus  
Email: janethereselazaroricafort@gmail.com

**Internship Period:**  
March 10, 2025 – May 30, 2025

*If ever you have questions about this project, feel free to message me. I’ll try my best to help out!*








