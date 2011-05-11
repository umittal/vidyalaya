<?php
$libDir="../dakhila/libVidyalaya/";
require_once "$libDir/db.inc";
require_once "$libDir/vidyalaya.inc";
require_once "$libDir/reports.inc";


function cityAllocation($students) {

	$tata = array();
	$town = array();

	foreach ($students as $id => $student) {
		if ($student->IsEnrolled) {
			$zip = $student->family->address->zipcode;
			$city = $student->family->address->city;
			if (array_key_exists($zip, $tata)) {
				$tata[$zip]++;
			} else {
				$tata[$zip]=1;
				$town[$zip] = $city;
			}
		}


	}
	ksort($tata);
	foreach ($tata as $zip => $number) {
		printf( "%2d, %s, %s\n", $number,  $zip, $town[$zip] );
	}
}

function cultureAge($students) {
	foreach ($students as $id => $student) {
		if ($student->IsEnrolled) {
			$dateOfBirth=strtotime($student->dateOfBirth);
			$age = (time() - $dateOfBirth)/60/60/24/365;
			printf ("%s, %s, %s, %d\n",
			$student->lastName, $student->firstName, $student->registration->culture->description, $age);
		}
	}
}

function printParentContact($parent, $family) {

	if (!empty($parent->email)) {
		print $parent->firstName . ", " . $parent->lastName . ", " . $parent->email .
			", \"" . $family->address->OneLineAddress() . "\", " . $parent->cellPhone . "\n";
	}
}

function printMemberList() {
	print "First Name, Last Name, E-mail Address, Home Address, Mobile Phone\n";
	foreach (Family::$objArray as $family) {
		printParentContact($family->mother, $family);
		printParentContact($family->father, $family);
	}
}

function checkWaitingList() {
	$fh = fopen("/tmp/a.csv", "r"); // address book
	$existing = array();
	while ($line = fgets($fh)) {
		$address = explode(",", $line);
		$email = trim($address[2]);
		print "$email\n";
		array_push($existing, $email);
	}

	$fh= fopen("/tmp/wait.csv", "r"); // wait list email addresses
	while ($line = fgets($fh)) {
		//print $line;
		$line = ereg_replace("^.*\<", "", $line);
		$line = trim(ereg_replace("\>.*$", "", $line));
		$found = array_search($line, $existing);
		if ($found)
		print "-----------------> remove " . $line . "\n";
		print $line . " Found = " . $found .  "\n";
	}
}

function BreakName($name) {
	if (empty($name)) return "";
	$data = preg_split("/[\s,]+/",$name, -1, PREG_SPLIT_NO_EMPTY);
	if (count($data) > 1) $lastname = $data[count($data)-1];
	$first = "First: ". ucfirst(strtolower($data[0]));
	if (count ($data) == 1) return $first;
	$last = "Last: ". ucfirst(strtolower($lastname));
	return $first . ",  " . $last;
}

function loadWaitingList() {
	$row = 1;
	$filename = "/home/umesh/admissions/wait.csv";
	$nextParent = 332;
	$nextChild = 1788;

	$outFile = "/tmp/newFile.sql";
	$outfh = fopen ($outFile, "w");
	
	$sql = "Delete from Parents2003 where ID >= $nextParent";
	fwrite($outfh, $sql . ";\n");
	$sql = "Delete from Students2003 where ID >= $nextChild";
	fwrite($outfh, $sql . ";\n");

	if (($handle = fopen($filename, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
			$pos = strpos($data[1],"@");

			if($pos === false) {
				// string needle NOT found in haystack
			}
			else {

				$email = $data[1];
				$priorityDate = date("Y-m-d", strtotime($data[2]));
				$homephone = $data[3];
				$motherName = $data[4];
				$motherCell = $data[5];
				$fatherName = $data[6];
				$fatherCell = $data[7];

				$child1name = $data[8];
				$child1dob = date("Y-m-d", strtotime($data[9]));
				$child1age = $data[10];
				$child1lang = $data[11];

				$child2name = $data[12];
				$child2dob = date("Y-m-d", strtotime($data[13]));
				$child2age = $data[14];
				$child2lang = $data[15];

				$child3name = $data[16];
				$child3dob = date("Y-m-d", strtotime($data[17]));
				$child3age = $data[18];
				$child3lang = $child2lang;
				$address = $data[19];
				
				$parentsColumns = array();
				$parentsColumns[] = "ID = $nextParent"; 
				
				$email = ereg_replace("^.*\<", "", $email);
				$email = trim(ereg_replace("\>.*$", "", $email));
				$parentsColumns[] = "M_EMAIL = \"$email\"";
				$parentsColumns[] = "priority_date = '$priorityDate'";
				$parentsColumns[] = "MH_PHONE = '$homephone'";
				

				printf ("\n$row: Email: %s, Home Phone: %s, Date: %s\n", $email, $homephone, $priorityDate);
				
				// mother
				if (!empty($motherName)) {
					print "Mother -> " . BreakName($motherName);
					$nameArray = preg_split("/[\s,]+/",$motherName, -1, PREG_SPLIT_NO_EMPTY);
					if (!empty($nameArray[0])) $parentsColumns[] = "MFIRST_NAME = '" .ucfirst(strtolower($nameArray[0])). "'";
					$count = count($nameArray);
					if ($count > 1) $parentsColumns[] = "MLAST_NAME = '" .ucfirst(strtolower($nameArray[$count-1])). "'";
				}
				if (!empty($motherCell)) {  
					print ", Cell: $motherCell\n";
					$parentsColumns[] = "MC_PHONE = '$motherCell'";
				} else { print "\n";};
				
				// father
				if (!empty($fatherName)) {
					print "Father -> " . BreakName($fatherName);
					$nameArray = preg_split("/[\s,]+/",$fatherName, -1, PREG_SPLIT_NO_EMPTY);
					if (!empty($nameArray[0])) $parentsColumns[] = "FFIRST_NAME = '" .ucfirst(strtolower($nameArray[0])). "'";
					$count = count($nameArray);
					if ($count > 1) $parentsColumns[] = "fLAST_NAME = '" .ucfirst(strtolower($nameArray[$count-1])). "'";
				}
				if (!empty($fatherCell)) {
					print ", Cell: $fatherCell\n";
					$parentsColumns[] = "FC_PHONE = '$fatherCell'";
				} else { print "\n";};
				
				// HIndi 29, gujarati 30, telugu 37

				// CHILD 1
				if (!empty($child1name)) {
					$studentColumns = array();
					$studentColumns[] = "ID = $nextChild";
					$studentColumns[] = "PARENT_ID = $nextParent";
					$studentColumns[] = "GENDER = 3";
					$studentColumns[] = "STATUS = 2";
					$studentColumns[] = "SS_CULT_NEXT = 99";
					$studentColumns[] = "SS_MUSIC_NEXT = 0";
					
					printf ("Child 1 -> %s", BreakName($child1name));
					$nameArray = preg_split("/[\s,]+/",$child1name, -1, PREG_SPLIT_NO_EMPTY);
					if (!empty($nameArray[0])) $studentColumns[] = "FIRST_NAME = '" .ucfirst(strtolower($nameArray[0])). "'";
					$count = count($nameArray);
					if ($count > 1) $studentColumns[] = "LAST_NAME = '" .ucfirst(strtolower($nameArray[$count-1])). "'";
					
					if (!empty( $child1dob)) {
						print ", DOB: $child1dob";
						$studentColumns[] = "DOB = '$child1dob'";
					}
					if (!empty($child1lang)) {
						print ", Lang: $child1lang";
						$lang = strtolower($child1lang[0]);
						unset ($langcode);
						if ($lang == "h") $langcode = 29;
						if ($lang == "g") $langcode = 30;
						if ($lang == "t") $langcode = 37;
						if (empty($langcode)) die ("could not find langcode for $child1lang\n");
						$studentColumns[] = "SS_LANG_NEXT = '$langcode'";
						
					}
					print "\n";
					
					$sql = "insert into Students2003 set " . implode (", ", $studentColumns) ;
					fwrite($outfh, $sql . ";\n");
					$nextChild++;
				}

				// CHILD 2
				if (!empty($child2name)) { 
					$studentColumns = array();
					$studentColumns[] = "ID = $nextChild";
					$studentColumns[] = "PARENT_ID = $nextParent";
					$studentColumns[] = "GENDER = 3";
					$studentColumns[] = "STATUS = 2";
					$studentColumns[] = "SS_CULT_NEXT = 99";
					$studentColumns[] = "SS_MUSIC_NEXT = 0";
					
					printf ("Child 1 -> %s", BreakName($child2name));
					$nameArray = preg_split("/[\s,]+/",$child2name, -1, PREG_SPLIT_NO_EMPTY);
					if (!empty($nameArray[0])) $studentColumns[] = "FIRST_NAME = '" .ucfirst(strtolower($nameArray[0])). "'";
					$count = count($nameArray);
					if ($count > 1) $studentColumns[] = "LAST_NAME = '" .ucfirst(strtolower($nameArray[$count-1])). "'";
					
					if (!empty( $child2dob)) {
						print ", DOB: $child2dob";
						$studentColumns[] = "DOB = '$child2dob'";
					}
					if (!empty($child2lang)) print ", Lang: $child2lang";
					print "\n";
					if (!empty($langcode)) $studentColumns[] = "SS_LANG_NEXT = '$langcode'";
					
					$sql = "insert into Students2003 set " . implode (", ", $studentColumns) ;
					fwrite($outfh, $sql . ";\n");
					$nextChild++;
				}
					
				if (!empty($child3name)) {
					printf ("Child 3 -> %s, DOB: %s, Lang: %s\n", BreakName($child3name), $child3dob,  $child3lang);
					$studentColumns = array();
					$studentColumns[] = "ID = $nextChild";
					$studentColumns[] = "PARENT_ID = $nextParent";
					$studentColumns[] = "GENDER = 3";
					$studentColumns[] = "STATUS = 2";
					$studentColumns[] = "SS_CULT_NEXT = 99";
					$studentColumns[] = "SS_MUSIC_NEXT = 0";
					
					
					printf ("Child 1 -> %s", BreakName($child3name));
					$nameArray = preg_split("/[\s,]+/",$child3name, -1, PREG_SPLIT_NO_EMPTY);
					if (!empty($nameArray[0])) $studentColumns[] = "FIRST_NAME = '" .ucfirst(strtolower($nameArray[0])). "'";
					$count = count($nameArray);
					if ($count > 1) $studentColumns[] = "LAST_NAME = '" .ucfirst(strtolower($nameArray[$count-1])). "'";
					
					if (!empty( $child3dob)) {
						print ", DOB: $child3dob";
						$studentColumns[] = "DOB = '$child3dob'";
					}
					if (!empty($child3lang)) print ", Lang: $child3lang";
					print "\n";
					if (!empty($langcode)) $studentColumns[] = "SS_LANG_NEXT = '$langcode'";
					$sql = "insert into Students2003 set " . implode (", ", $studentColumns) ;
					fwrite($outfh, $sql . ";\n");
					$nextChild++;
				}

				if (!empty($address)) {
					printf ("Home Address: %s\n", $address);
					$parentsColumns[] = "M_ADDRESS = '$address'";
				}
				
				$parentsColumns[] = "PRIMARY_EMAIL_FLAG = 3";
				$parentsColumns[] = "PRIMARY_PHONE_FLAG = 1";
				$parentsColumns[] = "TYPE_CODE = 5";
				$parentsColumns[] = "DIRECTORY_FLAG = 1";
				$parentsColumns[] = "COMMUNITY_EMAIL_FLAG = 1";
				
				$sql = "insert into Parents2003 set " . implode (", ", $parentsColumns) ;
				fwrite($outfh, $sql . ";\n");
				$nextParent++;
				$row++;
			}

		}
		fclose($handle);
	}
	fclose ($outfh);
}


function GetChildrenCountForFamily($id, $students) {
	$count=0;

	foreach ($students as $id2 => $student) {
		if (!empty($student->family)) {
			//			print "*** Student $id2 does not have a family";
//			return 0;

			if( $student->family->id == $id) {
				$count++;
			}
		}
	}
	return $count;
}


function listAllFamilies() {
	$students = GetAllData();
	
	foreach (Family::$objArray as $family) {
		if ($family->category->id != FamilyCategory::Waiting) continue;
		$children = $family->Children();
		$count = count($children);
		print "$family->id, $count\n";
		foreach ($children as $student) {
			print "\t$student->id, " . $student->fullName() . " \n";
		}
	}
	
	return;
	foreach (Family::$objArray as $family) {
		$count = GetChildrenCountForFamily($family->id, $students);
		print "$family->id, " . $family->parentsName() . ", " . $family->category->name . ", $count\n";
	}
	return;
	foreach (FamilyCategory::$objArray as $familyCategory) {
		print "$familyCategory->id" . ", " . $familyCategory->name . "\n";
	}
	return;
	
}

function listAllStudents() {
	GetAllData();

	$count=1;
	foreach (Student::$objArray as $student) {
		if (empty($student->studentStatus)) {
			print "Error: status is null for $student->id\n";
			continue;

		}

		// check isenrolled and status
		if ($student->IsEnrolled) {
			if ($student->studentStatus->id != StudentStatus::Active) {
				print "Error: $student->id is Enrolled but status is not active\n";
			}
			
			if (empty($student->languagePreference)) {
				print "Error: $student->id, familiy  does not have a language preference\n"; 
			}
			
//			print "Student, $student->id, $student->languagePreference, $student->firstGradeYear\n";
//			print "Update Students2003 set LanguageInterest = $student->languagePreference, YearFirstGrade = $student->firstGradeYear  where id=$student->id;\n";
		}

		if (empty($student->family)) {
			print "Error: Family is undefined for $student->id\n";
		}
		


		
		if ($student->IsEnrolled) {
			if (empty($student->dateOfBirth) || $student->dateOfBirth < '1975-01-01') {
				print "Error: Check the date of birth of $student->id, we have $student->dateOfBirth\n";
			}
			if (empty($student->registration->language)) {
				print "Error: Language is undefined for $student->id\n";
			} else {
//				print "$student->id, " . $student->LanguageInterest() . "\n";
			}

			if (empty($student->registration->culture)) {
				print "Error: Culture is undefined for $student->id\n";
			}

			$dateOfBirth=strtotime($student->dateOfBirth);
			$sessionStart = strtotime("2010-09-01");
			$age = ($sessionStart - $dateOfBirth)/60/60/24/365;
			
			if ($age != $student->AgeAt(Calendar::CurrentSession)) 
				printf ("Error: Age do not match for $student->id, before %s, Class %s\n", $age, $student->AgeAt(Calendar::CurrentSession));
			
			$cultureGrade = $student->registration->culture->description[1];
			$schoolGrade = 2010 - $student->firstGradeYear + 1;
			$ageAtFirstGrade = $age - $schoolGrade;
			if ($schoolGrade == 0) $schoolGrade="KG";
			
			if ($schoolGrade != $student->Grade()) 
			printf( "Error: Grades do not match for $student->id, before %s, Class %s\n", $schoolGrade, $student->Grade());
			
			if ((integer)$ageAtFirstGrade < 4 || (integer)$ageAtFirstGrade > 5 ) {
				printf ("%3d, %3d, %4d, %2s, %2d, %2s, %2d, %s\n",
				$count, $student->family->id, $student->id,  $cultureGrade, $age, $schoolGrade, $ageAtFirstGrade, $student->fullName());
					
				$count++;
			}
		}

	}
	return;

}

function AddEmailArray (&$emailArray, $email, $id) {
	if (!empty($emailArray[$email])) {
		print "Error: email $email already assigned to id $emailArray[$email], trying to assign it to $id\n";
	} else {
		$emailArray[$email] = $id;
	}
}

function HandlePraveenStyleEmail(&$emailArray, $praveenStyle, $id) {
	foreach (explode(";", $praveenStyle) as $email) {
		if (!empty($email)) {
			AddEmailArray($emailArray, $email, $id);
		}
	}
}

function utilTesting() {
	print Calendar::CurrentYear() ."\n";
}

function classUtilizationReport() {

//	$utilization = RoomUtilization::utilization(1, 0);
	$utilization = RoomUtilization::utilizationDept(1, 0, Department::Culture);
//	print "count of enrolled is " . count($utilization) . "\n";
}


function emailListForVasudha() {
	 foreach (Student::RegisteredStudents()  as $student) {
	 	 $fields = Array();
		 $fields[] = $student->id;
		 $fields[] = $student->fullName();
		 $fields[] = $student->GenderName();
		 $fields[] = sprintf("%2d", $student->Age());
		 $fields[] = implode("; ", $student->mailingListArray());

		 print implode (", ", $fields) . "\n";
	 }
}

function testExcelThing() {
	$workbook = new VidBook();
	$sheet = $workbook->setActiveSheet(0, "umesh is best", "haha");
	$workbook->fillData($sheet);
	$workbook->SaveWorkbook("/tmp/a.xlsx");
}

//testExcelThing();
classUtilizationReport();
//emailListForVasudha();

//	GetAllData();
// EmailCheck("manoj");
//EmailCheck("sujathakrishna28@yahoo.com");

//utilTesting();

//listAllStudents();

//listAllFamilies();

//loadWaitingList();

//checkWaitingList();

//$students = GetAllData();

//printMemberList();
//cityAllocation($students);
//cultureAge($students);


?>
