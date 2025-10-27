<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## News Aggregator App

The News Aggregator App is a RESTful API built with Laravel that allows users to aggregate articles from various news sources. It includes user authentication, article management, personalized news feeds, and more.

## Features

- **User Authentication**: Registration, login, logout, and password reset using Laravel Sanctum for token-based authentication.
- **Article Management**:
  - Fetch articles with pagination.
  - Search articles by keyword, date, category, and source.
- **User Preferences**: 
  - Manage preferences for news sources, categories, and authors.
  - Access a personalized news feed based on preferences.
- **Data Aggregation**:
  - Schedule commands to fetch articles from selected news APIs and store them in the local database.

=
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
