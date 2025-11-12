# IE-332-Group-29

Database. After logging into your group MySQL account (through the phpmyadmin website), go to
the import tab and upload the create_db.sql script. Be sure you do not have any tables with identical
names already in your database or errors could arise with them. The provided script has been tested
already and so there shouldn’t be any issues otherwise. You cannot change ANYTHING about the
database script/tables or add new ones. A Python script create_data.py to add data to the database
is also provided (caution, created by ChatGPT, so may have errors) and will generate INSERT data that
you can also upload through phpmyadmin.

Web interface functionality and design. This concerns how well the website works,
whether all functionality was properly provided, and correctness of plots and their aesthetics. It also
concerns programming the website, and whether the code is properly commented and organized (e.g.,
into multiple files, using CSS not inline style, external JavaScript). It does not consider SQL code.

SQL queries. There are many SQL queries needed to fulfill the requirements listed above,
although some are simpler than others. You must do as much work as possible in your queries, versus
selecting data and then performing calculations or other data processing in PHP/JavaScript.

Testing. Provide comprehensive functional testing https://www.youtube.com/watch?v=
5HBYg7_Onqo. That is, you want to test the functionality of the website- not the code directly. Below is
one option for formatting that tracks testing over time, which would create a table for each item being
tested (so you will likely have many tables). Grade is based on how rigorous/complete your testing was,
how you tracked and reported it, not whether you actually passed the tests or not (those errors are
deducted in part (b) or (c))

to start a port: python3 -m http.server 8080