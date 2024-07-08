+--------------------+--------------------------------------+------+-----+---------+----------------+
| Field              | Type                                 | Null | Key | Default | Extra          |
+--------------------+--------------------------------------+------+-----+---------+----------------+
| id                 | int(11)                              | NO   | PRI | NULL    | auto_increment |
| name               | varchar(255)                         | NO   | UNI | NULL    |                |
| geometry_type      | enum('Point','LineString','Polygon') | NO   |     | NULL    |                |
| geometry           | text                                 | NO   |     | NULL    |                |
| properties         | text                                 | NO   |     | NULL    |                |
| management_area_id | int(11)                              | YES  | MUL | NULL    |                |
| collections_id     | int(11)                              | YES  | MUL | NULL    |                |
+--------------------+--------------------------------------+------+-----+---------+----------------+