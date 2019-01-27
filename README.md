# benchmark-project
A simple app for comparing loading time of different sites

### Requirements:
* PHP 7.2
* Symfony 4.2
* and the [usual Symfony application requirements](https://symfony.com/doc/current/reference/requirements.html)

### Usage
There is a few things you have to know to use it properly:

Command that has to be run in order to use the app accepts one **required** parameter. 
That is an url of the site you want to compare. It should be it a full form, e.g.:
> htttp://www.google.com

So let's say you want to comapare two urls, the command will look like this:

> php bin/console app:benchmark http://www.google.com http://www.codewars.com 

You can compare as many sites as you want

If a main site loads slower than competitors you will get an email. If it loads twice as slow an sms would be sent as well.

In order for an email to be sent, specific information in .env file has to be filled.

Generated information is outputed in console and logged into *log.txt* file. 

In contains execution time for each url, difference between the it's loading time and main url and date of the test.

### Tests
Execute this command to run tests:

> $ cd my_project/

> $ ./bin/phpunit
