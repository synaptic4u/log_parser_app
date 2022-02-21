-----------------------------------------------------------------------------------------------
Log Parser App Setup & Usage
-----------------------------------------------------------------------------------------------

    I will be updating this when I have the chance to do so. There's still documentation I need to do for it!
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

    Then the application attempts to parse each file, either by line or whole file dumps.
    The parsing rules are configured in the Log Structure.

-----------------------------------------------------------------------------------------------
Log Structure
-----------------------------------------------------------------------------------------------

    This is the main configuration file for the application.
    The configuration consists of 4 main aspects:

    1   Log Include - log_include.
    2   Log Exclude - log_exclude.
    3   Dump - log_dump.
    4   File Exclude - file_exclude_types.

    Log Include
        Please rewad through the config.json file to see how I setup mine. My Apache2 log files are custom log files.

    Log Exclude
        This is a list of files that are on your server that you do not want to parse.
        In my list, I include files that are not UTF8 encoded, such as binary and database files.
        The app will break on these file types. Open a file and if it looks like gobbly-gook, 
        then add it to the exclude list.

    Dump
        You can specify files that you want to be dumped. 
        This is useful for log files whose structure is not consistent and may change.
        I find it useful for concurrent logging, where my auditing system will make an individual file for a single log.
        A log file can be parsed in two ways, where each line is inserted into a single column 
        in the database or where the whole file is in a single column.
        With the dump functionality, the file name is included into the row in another column.
        Further then that the file is not parsed.
        Line by line parsing uses the "\n" to extract lines.

    File Exclude
        Some files will be ignored automatically; like compressed archive files.
        Namely: ".zip", ".tar", ".gz", ".xz".
        The four types previously mentioned are hard coded to be ignored.
        I haven't written the application to parse anything other the UTF8 encoded file types.
        The code will break if it attempts to parse any other file encoding.
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
    that would parse all variations of log files. At this point that goal is not yet achievable.

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
            Friendly interaction withh the app is a goal for this.
-----------------------------------------------------------------------------------------------
Side Note
-----------------------------------------------------------------------------------------------

    This is my first attempt at programatically parsing log files in PHP.
    It's a little side project I wrote, so that I could make sense of all the log files from my server.
    
    I have thousands of files, with some files having hundreds of thousands of entries.
    The first time I ran this I had 80,000 log files; the largest having 200,000 lines.
    The size of the DB it generated was 4GB more or less.

    I have little experience with sysadmin, so I'm not a regex ninja, (which I find a little confusing...).
    I do have DB experience and my mind thinks in structured sets. 
    Tidy little rows and columns.
    
    I will be maintaining this project and refracoring it. 
    I wanted it to be something useful and something I could share. 
    I combed the internet looking for something free and simple to use.
    I hope that this is simple for you.

-----------------------------------------------------------------------------------------------
Contact
-----------------------------------------------------------------------------------------------

    If you have feedback; drop me a mail at emiledev@synaptic4u.co.za

    Thanks
    Emile De Wilde
