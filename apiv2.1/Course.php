<?php
require_once 'Assignment.php';
class Course{

	public $basePath;
	
	private $user;
	private $course_id;

    public function __construct($course_id = null) {
        
        if(!is_null($course_id)){
            $this->course_id = $course_id;            
        }
    }

    public function enrollStudent($course_id, $user_id){
	    if(isset($course_id) && isset($user_id)){
                $sql = "INSERT INTO course_student (user_id, course_id) VALUES ({$user_id}, {$course_id}) ";
                $this->query($sql);
                return true;
	    }
    }

    public function courseStudentsList($course_id){
	    global $db;
            if(!isset($course_id)){
                    return -1;
            }
            $studentList = $db->rawQuery("SELECT U.user_id as user_id, U.name as name, S.name as school_name
                    FROM course_student CS
                    JOIN user U ON U.user_id = CS.user_id
                    JOIN school S ON U.school_id = S.school_id
                    WHERE course_id = '$course_id' ");                        
            return $studentList;
    }

    public function courseList(){
	    global $db;
        $user_id = user::authService()['user_id'];
        if(!isset($user_id)){
            return -1;
        }
        //If user is teacher
        if(user::isTeacher() ){
	        $db->join('course_teacher T', 'T.course_id = C.course_id', "LEFT");
	        $db->where('T.teacher_id', $user_id);	       
	        $courseList = $db->get("course C");                       
            foreach($courseList as &$row){
                $row['studentList'] = $this->courseStudentsList($row['course_id']);                
            }
            return $courseList;
        }else{
            $courseList = $db->rawQuery("SELECT C.description AS description, C.name AS name, C.course_id AS course_id, C.course_code AS course_code
                     FROM course C
                     JOIN course_student CS ON CS.course_id = C.course_id                                          
                     WHERE CS.user_id = '$user_id'                      
                     ");      
            $data = array();
            foreach($courseList as $course){
	            $teacher = $db->rawQuery('SELECT CT.teacher_id as teacher_id, U.name as teacher_name, S.name as teacher_school
	            	FROM course C
	            	JOIN course_teacher CT ON CT.course_id = C.course_id
	            	JOIN user U ON U.user_id = CT.teacher_id
	            	JOIN school S ON S.school_id = U.school_id
	            	WHERE C.course_id = ' . $course['course_id']);
	            $course['teacher'] = $teacher;
	            $data[] = $course;
            }
            return $data;
        }
    }

    public function info($course_id = null){
	    global $db;
	    
	    if(is_null($course_id)){
		    $course_id = $this->course_id;
	    }
	    
        $user_id = user::authService()['user_id'];
        if(!isset($course_id) && !isset($user_id)){
                return -1;
        }

        if(user::isTeacher() ){
            //techer
	        $db->join('course_teacher T', 'T.course_id = C.course_id', "LEFT");
	        $db->where('T.teacher_id', $user_id);
	        $db->where('C.course_id', $this->course_id);	       
	        $row = $db->getOne("course C");           
        }else{
            //student
            $row = $db->rawQuery("SELECT C.description AS description, C.name as name, C.course_code as course_code, C.course_id as course_id
                    FROM course C
                    JOIN course_student CS ON CS.course_id = C.course_id
                    WHERE CS.user_id = {$user_id} AND C.course_id = '{$course_id}' ")[0];           
        }
        
		$teacher = $db->rawQuery('SELECT CT.teacher_id as teacher_id, U.name as teacher_name, S.name as teacher_school
            	FROM course C
            	JOIN course_teacher CT ON CT.course_id = C.course_id
            	JOIN user U ON U.user_id = CT.teacher_id
            	JOIN school S ON S.school_id = U.school_id
            	WHERE C.course_id = ' . $row['course_id']);                    
        $row['teacher'] = $teacher;         

        $row['studentList'] = $this->courseStudentsList($row['course_id']);        
        return $row;
    }

    public function courseAssignmentList($course_id = null){
	    global $db;
        $user_id = user::authService()['user_id'];
        if(is_null($course_id)){
            $course_id = $this->course_id;
        }
        if(!isset($course_id) && !isset($user_id)){
            return -1;
        }

        if(user::isTeacher() ){
            	                           
        }else{
            $db->where("status", 'publish');
        }
        
        $db->where("course_id", $course_id);
        $db->where("is_questionbank", 0);
        $db->where("is_deleted", 0);
        $data = $db->get("assignment");
        
        $assignments = array();
        foreach($data as $assignment){

            $assignments[] = Assignment::info($assignment['assignment_id']);
        }
        return $assignments;
    }


}