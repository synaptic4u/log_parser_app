-----------------------------------------------------------------------------------------------
Log Parser App Setup & Usage
-----------------------------------------------------------------------------------------------
    Intro:
    -------
        NB! -> Working in "dev" branch, will merge "dev" to "master" periodically. 
            Merged initial changes to master.
            New options.json file.
            You can choose what type of user interface you want to select: Web, CLI or Desktop.
            Can also choose what type of database to use: MySQL, PostgreSQL, SQLite.
            Will be refactoring the code for those options and also decoupling into behavioural patterns.


    Reason:
    -------
        Please note that there are great log parsing applications, but most will give you a web interface to use.
        I wanted a CLI option, as I dont want to open another door on the server that I need to protect.
        A web interface is something that I wish to avoid, the goal will be a CLI application and later bring in a web and desktop interface.
        Web & Desktop interface for running logs on local machines.
        I will be aiming for something like htop.

        I will be updating this when I have the chance to do so. There's still documentation I need to do for it!
        This is all running on a server where Im practicing my server administration and doing a little pentesting.

        I ended up with thousands of log files with hundreds of thousands of entries while I was testing. 
        I'm not a regex ninja, so I wrote this to be able to query everything from my DB where I am a ninja. 
        I have procedures where I clean the data and remove possible duplicates. 

    Please NB!!!
    ------------
        IMPORTANT!!! 
        This will produce duplicates, you will need to clean your data in mysql!!!!
        This is just something to get it started. My application logic still needs to be polished. 
            ex: in my "debug" log files it is including modsec-debug file, which should be in it's own table.
        I'll let you know when it is finished, hope it helps & have fun.
    
    Setup:
    ------
        The setup is comprised of 3 configuration files:
            1   Database - db_config.json
            2   Log Directories - config_path_list.json
            3   Log Structure - config.json

    Usage:
    ------
        Please read through this document, it's important!!!
        Log files need to be consistent and precisely setup in the config.json.
        Currently this app will only run on php8.1!!!
        Explanations and an example are provided.
        Read through your log files a little first to spot dicrepencies.
        I included the following files just so you can see how my setup was:
            /var/www/LogParserApp/app/src/logs/activity.txt
            /var/www/LogParserApp/app/src/structure_files/flattened.txt
            /var/www/LogParserApp/app/src/structure_files/result.txt
            /var/www/LogParserApp/app/src/structure_files/tree.txt
        Discrepencies can result in the failing of the parser.

-----------------------------------------------------------------------------------------------
Database
-----------------------------------------------------------------------------------------------

    This file contains the connection settings required for a MySQL database connection.
    
    The host setting is the name of your server.
    "host": "localhost",

    The user setting is the name of the MySQL user.
    "user": "logs",

    The MySQL user's password.
    "pass": "Peaches_And_Cream_!",

    The database name.
    "dbname": "logs"

    The user, password and database will need to be set in MySQL or MariaDB server.

    Connect to your database server and run the following:


        CREATE DATABASE IF NOT EXISTS `logs` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
        CREATE USER 'logs'@'localhost' IDENTIFIED BY 'Peaches_And_Cream_!'; 
        GRANT ALL PRIVILEGES ON `logs`.* TO 'logs'@'localhost' WITH GRANT OPTION;
        FLUSH PRIVILEGES;

    The first line creates a database in your server with character and collation settings.
    The second and third line will create a user with password and
    grant all priviliges for the user on the database you just created.
    Lastly we flush the priviliges to the database configuration.

    NB! Please note that in each table a primary key with auto incrementing is created.
        It will be named "logid" for all user defined tables or "dumpid" for the generic "log_dump" table.

-----------------------------------------------------------------------------------------------
Log Directories
-----------------------------------------------------------------------------------------------

    This configuration file tells the application in which directory / directories 
    where all the logs reside.

        "log_path" : [
            "/media/etc/other_etc/more_etc/parent_log_directory/",

            "/media/etc/other_etc/more_etc/even_more_etc/parent_log_directory/"
        ]

    This is the path to where the parent directory houses all the log files.
    The application will recursively cycle through all child directories and,
    creates a structured list of every file.
    I have all my log backups and I parse each parent_log_directory.

    Then the application attempts to parse each file, either by line or whole file dumps.
    The parsing rules are configured in the Log Structure.

-----------------------------------------------------------------------------------------------
Log Structure
-----------------------------------------------------------------------------------------------

    This is the main configuration file for the application.
    It is written in JSON formatting, which is read by the app and used as a std object.
    The configuration consists of 4 main aspects:

    1   Log Include - log_include.
    2   Log Exclude - log_exclude.
    3   Dump - log_dump.
    4   File Exclude - file_exclude_types.

    Log Include
        Please read through the config.json file to see how I setup mine. My Apache2 log files are custom log files.
        Some of my log files have a different formatting, I have changed the formatting a number of times. 
        It's noticeable in my Apache logs, I implement web server firewalls and auditing, so it writes lots of entries.
        I'm still playing with the structures and parsing.
        
        Structure:
            "daemon_error" : {
                "alias" : "daemon_error",
                "directory" :"/var/log/apache2",
                "name" : "daemon-error.log",
                "columns" : ["loggedon", "error", "client_ip", "unique_id", "log"],
                "loggedon_format" : "YYYY-MM-DD HH:mm:ss.m-6",
                "field_encapsulated" : "brackets or double quotes",
                "field_delimiter" : "space",
                "quirk" : "Unique_id & client_ip_port could be anywhere, we'll search for those values after error column. 
                           Date formatted as either: [Day MMM DD HH:mm:ss.m-6 YYYY] || [Day MMM DD HH:mm:ss.m-6 YYYY]" 
            },
            
            -> "daemon_error" -> The name of the log file.
            -> "alias" : "daemon_error" -> This is used as the alias for the database table name.
                    Alias must be one word, no hyphens or spaces!
                    Accepted values: tree or tree_log.
            -> "directory" :"/var/log/apache2" -> The expected directory. 
                    I plan to use this to differentiate log files with the same name based upon their directory path.
            ->  "name" : "daemon-error.log" -> The full name of the log file.
            ->  "columns" : ["loggedon", "error", "client_ip", "unique_id", "log"] -> The expected columns.
                    These will become the field names in the database table.
                    The last column "log" will default to all content in the row of the file as it is parsed. So after the initial named columns, 
                    all remaining content will be included into the "log" column.
            ->  "loggedon_format" : "YYYY-MM-DD HH:mm:ss.m-6" -> This is the expected date format in the file. 
                    Different applications use a different format and it can be problematic. 
                    I changed the way of creating dates in PHP to be more generic, but couldn't catch all the variances. 
                    If you encounter a different format, please email me and I will adjust the code.
                    So far I've encountered these variations in my log files:
                        "MMM DD HH:mm:ss"
                        "YYYY-MM-DD HH:mm:ss"
                        "YYYY-MM-DD HH:mm:ss,m-3"
                        "YYYY-MM-DD HH:mm:ss.m-6"
                    The differences formats used are parsed according to their letters with hyphens.
                        "YYYY" -> 2022
                        "MMM" -> Apr
                        "MM" -> 04
                        "DD" -> 24
                        "HH" -> 24 format
                        "ss,m-3" or "ss.m-6" -> Seconds with micro-seconds, deliminited by either a "," or "." and to the 3rd or 6th decimal place
            ->  "field_encapsulated" : "brackets or double quotes" ->   Fields are encapsulated with brackets or double quotes.
                    Some fields even though the "space" is used to delimit fields can be arranged in a group, 
                    yet still with spaces used as a delimiter in the group.
            ->  "field_delimiter" : "space" > Fields are normally delimited by a "space"
            ->  "quirk" : "Unique_id & client_ip_port could be anywhere, we'll search for those values after error column. 
                           Date formatted as either: [Day MMM DD HH:mm:ss.m-6 YYYY] || [Day MMM DD HH:mm:ss.m-6 YYYY]" 
                    -> "quirk" helped me to keep track of differences in formatting, which happened when I changed some application's log formatting.
                            Namely Apache2. I experimented with different formats.
                            I also experienced quirks when some applications would write their own errors to the log file, which used a different format,
                            like Fail2Ban for example.

    Log Exclude
        This is a list of files that are on your server that you do not want to parse.
        In my list, I include files that are not UTF8 encoded, such as binary and database files.
        The app will break on these file types. Open a file and if it looks like gobbly-gook, 
        then add it to the exclude list.

    Dump
        You can specify files that you want to be dumped. 
        This is useful for log files whose structure is not consistent and may change.
        I find it useful for concurrent logging, where my auditing system will make an individual file for a single log.
        A log file can be parsed in two ways, where each line is inserted into a single field 
        in the database or where the whole file is in a single column.
        With the dump functionality, the file name is included into the row in another fieild.
        Further then that the file is not parsed.
        Line by line parsing uses the "\n" to extract lines.

    File Exclude
        Some files will be ignored automatically; like compressed archive files.
        Namely: ".zip", ".tar", ".gz", ".xz".
        The four types previously mentioned are hard coded to be ignored.
        I haven't written the application to parse anything other then the UTF8 encoded file types.
        The code will break if it attempts to parse any other file encoding like binary.
        You can add to the list file types to be excluded.

-----------------------------------------------------------------------------------------------
Further Considerations
-----------------------------------------------------------------------------------------------

    You are more then welcome to contribute, provide insight and feedback.
    It's the first time I release something to the public, so be gentle!

    The app was primarily developed for the CLI, so it's very simplistic in it's UI,(non-existent).
    I did not have sufficient time to develop it better. Major rush job!
    Proper graceful error handling doesn't exist in it... It breaks and doesn't rollback.
    My knowledge of linux and logging is limited, some of the system log file columns I couldn't find adequate 
    explanations for them, so I guessed.
    I did notice that linux applications do follow their own format, the goal was to provide a generic architecture 
    that would parse all variations of log files. At this point that goal is not yet achieved.

    PLEASE NOTE!
    -------------
        Duplicated Data
            This app currently doesn't check for duplications in the database. 
            If you parse the same content twice, take a guess...
            Later I will introduce that.

        Error handling
            I will implement this as soon as I can.
            No one likes an app breaking on you and neither do I.
            Currently; if it breaks? 
                It's a full start over. 
            You can see the last file it was busy with in the activity log and restart from there.
            Edit the config.json accordingly, it's a schlepp!

        User Interface
            I will introduce a user interface in time.
            Friendly user experience with the app is a goal for this.
-----------------------------------------------------------------------------------------------
Side Note
-----------------------------------------------------------------------------------------------

    This is my first attempt at programatically parsing log files in PHP.
    It's a little side project I wrote, so that I could make sense of all the log files from my server.
    
    I have thousands of files, with some files having hundreds of thousands of entries.
    The first time I ran this I had 80,000 log files; the largest having 200,000 lines.
    The size of the DB it generated was 4GB more or less.

    I have little experience with sysadmin, so I'm not a regex ninja, (which I still need to learn properly).
    I do have DB experience and my mind thinks in structured sets. 
    Tidy little rows and columns.
    
    You will notice that PHPMailer is included in the dependancies. It is depreciated I think...
    I was planning to email reports at a later stage.
    Other dependancy that is used is composer's PSR4 autoloading.
    
    I will be maintaining this project and refractoring it. 
    I wanted it to be something useful and something I could share.
    I hope that this is simple for you.

-----------------------------------------------------------------------------------------------
Contact
-----------------------------------------------------------------------------------------------

    If you have feedback; drop me a mail at emile@synaptic4u.co.za or emiledewilde2@gmail.com

    Thanks
    Emile De Wilde
