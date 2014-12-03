## CSV Seeder


### Seed your database with CSV files

This package allows CSV based seeds.


### Installation

Require this package in your composer.json and run composer update (or run `composer require flynsarmy/csv-seeder:1.*` directly):

    "flynsarmy/csv-seeder": "1.0.*"


### Usage

Seed classes must extend `Flynsarmy\CsvSeeder\CsvSeeder`, they must define the destination database table and CSV file path, and finally they must call `parent::run()` like so:

	use Flynsarmy\CsvSeeder\CsvSeeder;

	class StopsTableSeeder extends CsvSeeder {

		public function __construct()
		{
			$this->table = 'gtfs_stops';
			$this->filename = app_path().'/database/seeds/csvs/stops.txt';
		}

		public function run()
		{
			// Recommended when importing larger CSVs
			DB::disableQueryLog();

			// Uncomment the below to wipe the table clean before populating
			DB::table($this->table)->truncate();

			parent::run();
		}
	}

### Configuration

In addition to setting the database table and CSV filename, two other configuration options are available. They can be set in your class constructor:

 - `insert_chunk_size` (int 500) An SQL insert statement will trigger every `insert_chunk_size` number of rows while reading the CSV
 - `csv_delimiter` (string ,) The CSV field delimiter.
 - `hashable` (string password) Hash the hashable field, useful if you are importing users and need their passwords hashed. Uses `Hash::make()`

### License

CsvSeeder is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
