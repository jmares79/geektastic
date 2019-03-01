Geektastic Transactions Printer
=========================

The objective for this program is to demonstrate OOP and unit testing skills.

### Task
Create a simple report that shows transactions for a merchant id specified as command line argument.
The data.csv file contains dummy data in different currencies, the report should be in GBP.

### Installation
Clone the repo to your preferred folder and execute "composer install".

### Usage
From the project command line, execute "php bin/console app:create-report {id}", being the {id} the Merchant Id. Not providing an Id will throw an error.

### Architecture

The software is created based in Symfony framework (without some unnecesary bundles); the reason for using this framework is that it is a modern, up to date one, with all the neccesary pieces for constructing a powerful project (Doctrine & Twig  out of the box, etc).

Important to be noticed is that bundles that were not required for the project were removed from the composer file.

Also, as a final reason, is that nowadays all the big projects are based on a framework, being custom or standard, so using one makes sense in order to practice at least one of them.

If you want to check how a non-framework project, in a way similar to this, check [here](https://gitlab.com/jmares79/consumer)

Project structure
-----------------

The project is structured using the standard Symfony bundling strategy, where each bundle wraps all the needed Controller, Services, Views and Routes for the specific bundle.

However, as this project is thinking as an exam/test with time constraints, the per-bundle configuration was ommited, having the main config/services/parameters files used instead (in the main [config](https://github.com/jmares79/awin/tree/master/app/config) folder).

The project is configured with the following bundles:

  * ReportBundle - This bundle wraps the creation of the main command to be executed and the Report Service, which provides the
"createReport" & "printReport" methods being called by the command. Also, for the sake of not creating another bundle just for that, it wraps "OutputStdPrinterService", that provides the stdout functionality.

 * MerchanBundle - This bundle provides, as a way of a "controller", the nexus between the report bundle and the rest of other bundles explained below. The mail purpose is to fetch the transactions from a data source/stream provided, and to return the transactions converted, using a converter service, also provided in another bundle.

 * CurrencyBundle - This bundle provides the functionality to both the conversion capabilities, as well as a service to get the current exchange rates (both are separated in different services). The converter service provides a method for convert any amount to GBP, while the exchange rate service provides that capability using a getExchangeRate method. the exchange rate was created as a fixed array with static values for test purposes only.

 * StreamDataBundle - This bundle provides an abstratction layer for fetching the transactions from the specific data type. While this test provides a CSV file, any data type can be used: another file type, a database, an AWS etc. The means to add these will be pointed below.

 Extending or modifying the project
 ----------------------------------

 The point of every project is, besides achieve what is expected, being prepared for managing change. That means, extend and modify the project as painless as possible.

 For that, I did my best in structuring following OOP & SOLID techniques, allowing extending easily.

 Every service implements a series of interfaces, allowing the creation and use of any other concrete service easy.

 As the Dependency injection in the services expects interfaces, as long as that contract is followed, one can create a family of services for each bundle, and those will still be accepted in the contract, leaving the bulk part of the project untouched.

 The specific way of doing that is, using [report service](https://github.com/jmares79/awin/blob/master/src/ReportBundle/Service/ReportService.php) as an example:

 1. Create a new service inside "Service" folder that implements the interface provided
 2. Modify (or set an strategy-type for a family of services) the [service.yml](https://github.com/jmares79/awin/blob/master/app/config/services.yml) file accordingly, setting the new arguments for the service.
 3. Create/modify tests of the service if needed.

 The rest of the services can be modified in the same way.

 As it can be seen, modifying/extending is easy, providing as much as possible an Open-Closed principle.


 Execution path
 --------------

 When executing the program as explained before, the path of execution goes like this (all needed services are injected through dependency injection):

 1. ReportCommand configures & executes the report service.
 2. ReportService get the Id passed and, via MerchantService, fetches and converts the transactions.
 3. MerchantService parses the transaction from the CSV file using a StreamData service, and converts them to GBP using a currency service.
 4. StreamData open and parses the file, returning the raw data.
 5. Currency get the exchange rate and converts the amount passed.
 6. ReportService, via Outputprinter, shows the content to the stdout/console as expected

 Tests
 -----

 Tests are provided for the public methods (aka interface) that the user/system will use. There's planty of discussion on wheter private also has to be tested.

 Without entering any of those discussions, private methods here were not tested, to keep the time scale of the test just fine.

 The strategy adopted is the standard for PHPUnit test doubles documentation. This is, mocking any class dependencies, setting the output of helper classes accordingly.

 This strategy is provided with the `createMock()` method that PHPUnit provides. The service dependencies were created following the `<MyServiceClass>::class` syntax, and then any needed method response was mocked using the `->method(<mymethod>)->willReturn(<response>);` syntax.

 Tests are provided for the following bundles:

  * CurrencyBundle - Tests covers both CurrencyConverter & StaticCurrencyExchange.
  * FileMerchantBundle - Tests covers FileMerchantService.
  * StreamDataBundle - Tests covers StreamDataService.
  * ReportBundle - Tests covers OutputStdPrinterService.

  Report creation tests were not provided for some reasons. First, the classes were created to use internal attributes, which has to be mocked using Reflection, which would take longer; and second because the `createReport()` method simply calls methods of FilerMerchant that were already been tested, so it would duplicate tests.

  However the `show()` method was indeed tested using the output buffering capacilities of PHP, so the response could be watched on any change for errors.

  Improvements
  ------------

  I thought a series of improvements to be made to the project:

  * Argument parsing, allowing different showing methods (example --table for print in table format, --file=path/to/file, --streamtype=mysql, etc)
  * A family of strategies for handling the previous options
  * Expose functionality through an API, creating Controllers & Routes (NOT provided)
