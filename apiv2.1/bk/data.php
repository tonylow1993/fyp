<?php
require_once "mysql.php";

/**
 * data class.
 *
 * @extends mysql
 */
class data extends mysql{

	public $basePath;
	public $info;
	//private $course_id;
	private $group_id;

    /**
     * __construct function.
     *
     * @access public
     * @param mixed $course_id
     * @return void
     */
    public function __construct($group_id = null) {
        parent::__construct();
        $this->basePath = dirname(__FILE__);
        //Individual Course
        if(!is_null($group_id)){
	        //$this->info = $this->assignmentInfo($assignment_id);
	        $this->group_id = $group_id;
        }
    }
	
	public function uploadCollaborationData($group_id, $text, $voice, $drawing, $code){
		if(isset($group_id)){
			$user_id = user::authService()['user_id'];
			$sql = "INSERT INTO collaboration_data (group_id, user_id, text_msg_no, voice_msg_no, drawing_no, code_no) VALUES ('1', '{$user_id}', '{$text}', '{$voice}', '{$drawing}', '{$code}')";
			$this->query($sql);
		}
	}
	
	public function getCollaborationData() {
			$sql = "SELECT U.name as username, SUM(C.code_no) as code_no, SUM(C.text_msg_no) as text_msg_no, SUM(C.voice_msg_no) as voice_msg_no, SUM(C.drawing_no) as drawing_no FROM collaboration_data C JOIN user U ON C.user_id = U.user_id WHERE C.group_id = '1' GROUP BY C.user_id";
			$result = $this->query($sql);
			$data = array();
			if ($result->num_rows != 0){
				while($row = $result->fetch_assoc()){
					$data[] = $row;
				}
			}
			return json_encode($data);
	}
	
	public function getTestcaseData() {
			$sql = "SELECT result, COUNT(*) as compile_no FROM testcase_data WHERE group_id = '1' GROUP BY result";
			$result = $this->query($sql);
			$data = array();
			if ($result->num_rows != 0){
				while($row = $result->fetch_assoc()){
					$data[] = $row;
				}
			}
			return json_encode($data);
	}
	
	public function getFailData (){
			$sql = "SELECT T1.group_id, COUNT(*) as fail FROM (SELECT group_id, MIN(data_id) AS min FROM testcase_data WHERE result = 'PASS' AND testcase_id = '1' GROUP BY group_id) AS T1 LEFT JOIN (SELECT group_id, data_id FROM testcase_data WHERE result = 'FAIL' AND testcase_id = '1') AS T2 ON T1.group_id = T2.group_id WHERE T2.data_id < T1.min GROUP BY group_id";
			$result = $this->query($sql);
			$data = array();
			if ($result->num_rows != 0){
				while($row = $result->fetch_assoc()){
					$data[] = $row;
				}
			}
			return json_encode($data);
	}
	
	public function getPassData (){
			$sql = "SELECT group_id, COUNT(*) as pass FROM testcase_data WHERE result = 'PASS' AND testcase_id = '1' GROUP BY group_id";
			$result = $this->query($sql);
			$data = array();
			if ($result->num_rows != 0){
				while($row = $result->fetch_assoc()){
					$data[] = $row;
				}
			}
			return json_encode($data);
	}
	
	public function getAttemptData (){
			$sql = "SELECT group_id, COUNT(*) as attempt FROM testcase_data WHERE testcase_id = '1' GROUP BY group_id";
			$result = $this->query($sql);
			$data = array();
			if ($result->num_rows != 0){
				while($row = $result->fetch_assoc()){
					$data[] = $row;
				}
			}
			return json_encode($data);
	}
	
	public function getTestcase (){
			$sql = "SELECT testcase_id, input, output, type, description FROM assignment_testcase WHERE assignment_id = '1'";
			$result = $this->query($sql);
			$data = array();
			if ($result->num_rows != 0){
				while($row = $result->fetch_assoc()){
					$data[] = $row;
				}
			}
			return json_encode($data);
	}
	
	public function getAssignmentGroup () {
			$sql = "SELECT COUNT(*) as groupno FROM assignment_group WHERE assignment_id = '1'";
			$result = $this->query($sql);
			if ($result->num_rows != 0){
				$row = $result->fetch_assoc();
			}
			return json_encode($row);
	}
}