<?php
require_once('Simpla.php');

class Files extends Simpla
{

	public function add_file($file)
	{
		$file = (array)$file;
		if(empty($file->date))
			$set_curr_date = ', date=now()';
		$this->db->query("INSERT INTO __files SET ?%$set_curr_date", $file);
		return $this->db->insert_id();
	}

	public function get_file($id)
	{
		if(is_int($id))			
			$filter = $this->db->placehold('id = ?', $id);
		else
			$filter = $this->db->placehold('url_id = ?', $id);
		$query = "SELECT id, url_id, url, file, date, dl_count, dl_date, file_size, user_id FROM __files WHERE $filter LIMIT 1";
		$this->db->query($query);
		return $this->db->result();
	}
	public function get_file_by_url($url)
	{
		$filter = $this->db->placehold('url = ?', $url);
		$query = "SELECT id, url_id, url, file, date, dl_count, dl_date, file_size, user_id FROM __files WHERE $filter LIMIT 1";
		$this->db->query($query);
		return $this->db->result();
	}

	public function update_url($id, $url)
	{
		$query = $this->db->placehold("UPDATE __files SET url=? WHERE id=? LIMIT 1", $url, intval($id));
		$this->db->query($query);
		return $id;
	}

	public function update_dl($id){
		$this->db->query("UPDATE __files SET dl_count=dl_count+1, dl_date=now() WHERE id=?", intval($id));
	}

	public function get_files($filter = array()){
		$order = 'id';
		$user_id_filter = '';
		if(!empty($filter['order'])){
			if($filter['order']=='id')
				$order = 'id';
			elseif($filter['order']=='file')
				$order = 'SUBSTR(file , 40)';
			elseif($filter['order']=='file_size')
				$order = 'file_size';
			elseif($filter['order']=='url')
				$order = 'url';
			elseif($filter['order']=='date')
				$order = 'date';
			elseif($filter['order']=='dl_date')
				$order = 'dl_date';
			elseif($filter['order']=='dl_count')
				$order = 'dl_count';
		}
		if(!empty($filter['direction']) && $filter['direction']=='desc'){
			$order .= ' DESC';
		}else{
			$order .= ' ASC';
		}
		if(!empty($filter['user_id'])){
			$user_id_filter = $this->db->placehold("AND user_id=?",$filter['user_id']);
		}
		$query = $this->db->placehold("SELECT id, url_id, url, file, date, dl_count, dl_date, file_size, user_id FROM __files WHERE 1=1 $user_id_filter ORDER BY ".$order);
		$this->db->query($query);

		return $this->db->results();
	}

	public function delete_file($id){		
		$file = $this->get_file($id);
		unlink($file->file);
		rmdir(dirname($file->file));
		$this->db->query("DELETE FROM __files WHERE id=? LIMIT 1", intval($id));
	}
	public static function human_filesize($bytes, $decimals = 2) {
		$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . '&nbsp;' . @$size[$factor];
	}
}