# WeightTracker
A simple daily weight tracker based on PHP and AJAX

The intention of this application is to provide a weight tracking solution that is slightly less unwieldy than a forever-growing spreadsheet. The main goal is for this to perform very quickly even if the database has several years worth of data in it. It also holds some basic information about you so that it can show you BMI/BMR.

At the time of initial commit, this is based on SQLite 3 and has a data access layer that can be added to with classes for other back ends. It is a PHP app that depends on the SQLite PDO driver and was written/tested on PHP 8 (FPM), Lighttpd 1.4.63 and Chrome.

It makes use of Google Charts to present a line chart of data.
