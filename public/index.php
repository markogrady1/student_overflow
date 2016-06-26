<?php
use App\DB\ForumDB;
?>
<html>
<head>
    <title>Student Overflow</title>
</head>
<body>
THIS IS THE HOME PAGE. i dont know what this means but i will edit it with sabha
<!--THIS IS THE HOME PAGE FOR THE STUDENT OVERFLOW SITE -->
<?php
$db = new ForumDB();
$result3 = $db->insert("question", array("question" => "yo is my name is mark", "created_by "=> "mark" ));
//$result4 = $db->paginate(1, "question", 7);
$result5 = $db->paginate(0, "question", 100);

echo $result5[0]["question"];
d($result5);
?>
</body>
</html>