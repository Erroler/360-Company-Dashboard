![logo](https://i.imgur.com/SUGbX1s.png)

> **This is a web app aimed to provide a complete overview of the company status**, regarding the information that is contained on the [SAF-T](https://en.wikipedia.org/wiki/SAF-T) file (standard audit file for tax purposes) and information regarding sales orders, purchases, inventory, and accounts payable, using a dashboard to provide high level/graphical information with drill down functionalities to products/ supplier/ purchases. 
> 
> Integrates with the [Jasmin accounting software](https://www.jasminsoftware.pt/).
> 
> This project was developed as a course assignment of the [Information Systems subject](https://sigarra.up.pt/feup/en/UCURR_GERAL.FICHA_UC_VIEW?pv_ocorrencia_id=436456) at [Faculdade de Engenharia da Universidade do Porto](https://sigarra.up.pt/feup/en).
> 
> For a complete list of features see the [project report](https://github.com/Erroler/360-Company-Dashboard/blob/master/report.pdf).

## Screenshots

* [Login page](https://i.imgur.com/YJ2lQs1.png)
* [Overview](https://i.imgur.com/GCp1mol.png)
* [Finances](https://i.imgur.com/UrxGsjz.png)
* [Sales](https://i.imgur.com/qxOJEBK.png)
* [Purchases](https://i.imgur.com/xNjN02s.png)
* [Inventory](https://i.imgur.com/kCO1Ymx.png)
* [View Consumer](https://i.imgur.com/893W2Pp.png)
* [View Supplier](https://i.imgur.com/wTtSVt2.png)
* [View Product](https://i.imgur.com/C2vDb4z.jpg)
* [Inventory Search](https://i.imgur.com/Cc3sFz7.png)
* [Orders Search](https://i.imgur.com/Fx1Z8E5.png)
* [Purchases Search](https://i.imgur.com/UM1tggv.png)
* [About the Company](https://i.imgur.com/0zZFfPZ.png)
* [Profit and Loss Statement](https://i.imgur.com/9whaI8t.png)
* [Trial Balance Sheet](https://i.imgur.com/hHopJjY.png)
* [Balance Sheet](https://i.imgur.com/WdjNgVY.png)

## Setup

Requirements
> * PHP 7.1+
> * Composer

> ### 1. Install dependencies.
> ``` 
> composer install
> ```
> 
> ### 2. Rename .env.example to .env and fill the following fields:
> ```
> # Database related
> DB_CONNECTION=
> DB_DATABASE=FILL_THIS
> DB_USERNAME=FILL_THIS
> DB_PASSWORD=FILL_THIS
> 
> # Jasmin software API user
> JASMIN_APPLICATION_ID=
> JASMIN_APPLICATION_SECRET=
> ```
> 
> ### 3. Generate a new Laravel app key.
> ```
> php artisan key:generate
> ```
> 
> ### 4. Run database migrations and seed tables.
> ```
> php artisan migrate:refresh --seed
> ```
> 
> ### 4. Start development server.
> ```
> php artisan serve
> ```
> ### 5. Access project at localhost:8000
> ### 6. Login with the following credentials:
> ```
> User: admin@admin.com
> Password: admin
> ```

### Libraries/Frameworks used

* [Laravel](https://laravel.com/)
* [Tabler](https://tabler.io/)
* [Chart.js](https://www.chartjs.org/)
* [Datatables](https://datatables.net/)

### Developed by
* Ana Sá Silva (up201604105@fe.up.pt)
* Fábio Oliveira (up201604796@fe.up.pt) 🠈 Me
* Hugo Fernandes (up201909576@fe.up.pt)
* Pedro Pinho (up201605166@fe.up.pt)
* Ricardo Moura (up201604912@fe.up.pt)
