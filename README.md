<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<p align="center">
<strong>If you need a ready-made solution to manage IT support for small to medium teams or clients, contact us at <a href="mailto:info@ticketflowai.com">info@ticketflowai.com</a></strong>
</p>

## About TicketFlowAI

TicketFlowAI is a web application that helps manage tech support tickets to streamline an IT department helpdesk environment. It is comprised of two parts, and this is the back end with the APIs. TicketFlowAI connects to AWS AI services such as Comprehend and Bedrock to classify ticket priority, complexity, and the need for human interaction according to a model trained on custom data from each company. It can also provide automated answers for simple queries that don't need human interaction based on custom documentation from each company.

TicketFlowAI is created with Laravel 11 and is compatible with PHP 8.1 and higher.

## Getting Started

To run this application, follow these steps:

1. Pull the repository.
2. Copy the `.env.example` file to `.env` and fill in the required fields.
3. Run `composer u` to update dependencies.
4. Run `composer i` to install missing dependencies.
5. Run `npm i` and `npm run build` to load the JavaScript files and dependencies for Swagger.
6. Configure the link structure for resetting passwords in `AppServiceProvider.php`.

## Security Vulnerabilities

If you discover a security vulnerability within TicketFlowAI, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The TicketFlowAI framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
