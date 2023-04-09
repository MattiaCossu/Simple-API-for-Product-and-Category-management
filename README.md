# Simple-API-for-Product-and-Category-management

This project provides a simple RESTful API for managing products and categories.

## Requirements

- PHP 8.0 or higher
- MySQL
- Docker(Optional)

## Installation

To run this project, you will need to have Docker installed on your system. Once Docker is installed, you can follow these steps to set up the project:
1.  Clone the repository to your local machine.
``` bash
    git clone https://github.com/your-username/your-repo.git
```
2.  Navigate to the project directory.
``` bash
    cd your-repo
```
3.  Build the Docker containers.
``` bash
    docker-compose build
```
4. Start the containers.
``` bash
    docker-compose up -d
```
In this repo you will find both the db and the apche server (Dockerfile)

## API Documentation

Here is a brief summary of the available endpoints:

* GET /api/products: Returns a list of all products.
* GET /api/products/{id}: Returns a specific product by ID.
* GET /api/products/category/{category}: Returns a list of products in a specific category.
* POST /api/products: Creates a new product.
* PATCH /api/products/{id}: Updates a specific product by ID.
* DELETE /api/products/{id}: Deletes a specific product by ID.
* GET /api/categories: Returns a list of all categories.
* GET /api/categories/{id}: Returns a specific category by ID.



