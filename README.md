# The UNL faculty staff and student directory.

The UNL_Peoplefinder package contains all the source code for the UNL directory.
Additionally, this package provides an API which developers can use to retrieve
directory information and perform searches.

## Maintenance Tasks

The Officefinder Departments & Units portion of the site is stored in a mysql database.

## Editors:

Editors can be added for any Departments & Units listing and permissions are
hierarchical. We expect 

When the print "Centrex" directory stopped being published, we used the HR
Contact List (go.unl.edu/hrcontacts) to assign a default user to each
department. This list should be updated on a quarterly basis.

The script to update permissions is `data/setup_permissions.php`

Once we have accurate editor information, it's best to work with Linda Geisler
and send a note to all the HR Contacts (SAP coordinators) via the
`scripts/mail_sap_coordinators.php` script.

## Data sources

This project uses and combines data from many different sources

## HR and orgunit information

Jim Liebgott maintains a process which exports the hr_tree.xml file. This file
contains the UNL departmental hierarchy, which is used in the directory.
Jim's process connects to CSN's data warehouse and exports the XML data.

The script `data/update_hr_tree.php` runs nightly (via cron) to find new or
moved units and add/update them in the database. 

The script `scripts/flag_sap_orgs_without_employees.php` runs nightly to mark
units which have no employees or child units and hide them from the directory.

## Faculty data

Faculty data is retrieved from Activity Insight. There is a cron job that runs regularly to refresh that data. This data is stored in the cache.

```
php scripts/cache_knowledge.php
```

## Active Directory

Most of the data for faculty, staff, and students is retrieved from Active Directory via LDAP queries.

## IAM Views

The active directory data does not contain all of the information we need (email and employment appointments for example). We augment the active directory information via Oracle database views provided from Identity and Access Management.

## Student data

Student data is provided by Terry Pramberg (Student Information Systems). He provides us with .txt files (which are actually csv files) that contain public student directory information. There are two files:

1) `data/unl_sis_bio.txt` which contains the NUID and class level for students.
2) `data/unl_sis_prog.txt` which contains the majors for each student.

These files need to be manually uploaded to the production server on a regular basis.

There is a script that combines these files and caches the result in memcache for each student. As data is retrieved from the directory, these results are merged with their appropriate Active Directory records.

To update this data, run

```
php scripts/import_sis_data.php
```

Note: this needs to be ran at least once a day to keep the data in the cache

## Sample User (Optional)

The idea is to simulate a person record that doesn't exist in the official identity system.

Reasons to do this:
- So there is a permanent person (that won't quit or retire) for another system to use for testing.
- As a test/development record for possible schema changes or to see how different data affects output.
- As an Easter egg.

To enable and use the sample user:
To use:
1) Copy /data/test-data.inc_sample.php to /data/test-data.inc.php and edit if desired.
2) Make sure the include of /data/test-data.inc.php and the setting of UNL_Peoplefinder::$sampleUID are uncommented in config.inc.php


## INSTALL

1) Run: 'npm install; grunt; composer install'
2) Copy www/config-sample.inc.php to www/config.inc.php and add your LDAP credentials or uncomment the webservice driver line


## Rebuild cache

```
php scripts/rebuild_cache.php
```

This will:
1) flush the entire cache
2) populate the cache with SIS data
2) populate the cache with faculty data

Other data will be cached as when data is retrieved from Active Directory and the IAM views.
