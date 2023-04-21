# WeightTracker
A simple daily weight tracker based on PHP and AJAX

The intention of this application is to provide a weight tracking solution that is slightly less unwieldy than a forever-growing spreadsheet. The main goal is for this to perform very quickly even if the database has several years worth of data in it. It also holds some basic information about you so that it can show you BMI/BMR.

The database technology used by the application is abstracted from the serverside code using the "WeightDAO" Data Access Object interface. The author of this application is maintaining DAOs for both MariaDB and SQLite 3, but you could create your own DAO for whatever backend you like by creating a class that extends "DataAccessObject" and implements "WeightDAO", and then making minor changes to "WeightDatabase"

If you want to make use of SQLite 3 or MariaDB, your PHP installation should have PDO drivers installed for your desired technology.

A simple daily landing page allows you to enter the data for the current day, and another page allows you to view past data, or export it as a CSV.

The app makes use of Google Charts to present a line chart of data.

It is vanilla Javascript and PHP throughout, with the only dependency being Bootstrap 5.3, included via CDN.

![image](https://user-images.githubusercontent.com/59516714/233742319-08e6b47c-5a6e-4055-9dc6-a56e70c33e86.png)

![image](https://user-images.githubusercontent.com/59516714/233742986-702eff70-8f1a-4d97-b638-4c6efbf3d853.png)

![image](https://user-images.githubusercontent.com/59516714/233743193-884545c2-83d9-42e7-9b11-20230708c9d8.png)
