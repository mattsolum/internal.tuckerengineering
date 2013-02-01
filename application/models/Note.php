<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Note extends CI_Model {
	
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();	
	}
	
	public function get_by_note($id)
	{
		$this->CI->db->select('notes.*, users.name, users.email');
		$this->CI->db->from('notes');
		$this->CI->db->join('users', 'notes.user_id = users.user_id');
		$this->CI->db->where('notes.note_id', $id);
		$this->CI->db->limit(1);

		$query = $this->CI->db->get();

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			$note = new StructNote();
			
			$note->id			= $row->note_id;
			$note->type_id		= $row->id;
			$note->type			= $row->type;
			$note->text			= $row->note;
			$note->date_added	= $row->date_added;
			
			$note->user->id		= $row->user_id;
			$note->user->name	= $row->name;
			
			$note->user->set_email($row->email);

			return $note;
		}

		return FALSE;
	}
	
	public function get_by_job($id)
	{
		return $this->get($id, 'job');
	}
	
	public function get_by_property($id)
	{
		return $this->get($id, 'property');
	}
	
	public function get_by_client($id)
	{
		return $this->get($id, 'client');
	}
	
	//Returns empty array if nothing is found.
	public function get($id, $type)
	{
		$where = array('id' => $id, 'type' => $type);
		
		$this->CI->db->select('notes.*, users.name, users.email');
		$this->CI->db->from('notes');
		$this->CI->db->join('users', 'notes.user_id = users.user_id');
		$this->CI->db->where('notes.id', $id);
		$this->CI->db->where('notes.type', $type);
		$this->CI->db->order_by('notes.date_added', 'ASC');
		
		$query = $this->CI->db->get();
		
		if($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result() AS $row)
			{
				$note = new StructNote();
				
				$note->id			= $row->note_id;
				$note->type_id		= $row->id;
				$note->type			= $row->type;
				$note->text			= $row->note;
				$note->date_added	= $row->date_added;
				
				$note->user->id		= $row->user_id;
				$note->user->name	= $row->name;
				
				$note->user->set_email($row->email);
				
				$result[] = $note;
				unset($note);
			}
			
			return $result;
		}
		
		return array();
	}
	
	public function commit($notes)
	{
		if(!is_array($notes))
		{
			$notes = array($notes);
		}

		if(count($notes) > 0)
		{
			$type 	= strtolower($notes[0]->type);
			$id 	= $notes[0]->type_id;
		
			foreach($notes AS $note)
			{
				if($note->is_valid())
				{
					$this->commit_single($note);
				}
				else
				{
					log_message('Error', 'Note is not valid!');
				}
			}
			
			$this->CI->db->trans_complete();
			
			if($this->CI->db->trans_status() === FALSE)
			{
				log_message('Error', 'Error in Note method commit: transaction failed.');
				return FALSE;
			}
			else
			{
				$this->Event->trigger($type . '.dirty', $id);
				return TRUE;
			}
		}
		else return TRUE;
	}
	
	private function commit_single($note)
	{
		$this->CI->db->trans_start();
		
		if($note->id != NULL)
		{
			$this->update($note);
		}
		else
		{
			$this->create($note);	
		}
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Note method commit_single: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	private function create($note)
	{
		$this->CI->db->trans_start();
		
		$data = array();
		
		$data['id']			= $note->type_id;
		$data['type']		= $note->type;
		$data['user_id']	= $note->user->id;
		$data['note']		= $note->text;
		$data['date_added']	= now();
		
		$this->CI->db->insert('notes', $data);
	
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Note method create: transaction failed.');
			return FALSE;
		}
		else
		{
			return $this->get_id($data['user_id'], $data['date_added']);
		}
	}
	
	private function update($note)
	{
		$this->CI->db->trans_start();
		
		$data = array();
		
		$data['note']		= $note->text;
		
		$this->CI->db->where('note_id', $note->id);
		$this->CI->db->update('notes', $data);
		
		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Note method update: transaction failed.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function delete($note_id)
	{
		$this->CI->db->trans_start();
		
		$note = $this->get_by_note($note_id);

		$where = array('note_id' => $note_id);
		
		$this->CI->db->delete('notes', $where);
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('Error', 'Error in Note method delete: transaction failed.');
			return FALSE;
		}
		else
		{
			$this->CI->Event->trigger($note->type . '.dirty', $note->type_id);
			return TRUE;
		}
	}
	
	private function get_id($user_id, $date)
	{
		$where = array('user_id' => $user_id, 'date_added' => $date);
		
		$query = $this->CI->db->get_where('notes', $where);
		
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			
			return $row->note_id;
		}
		
		return FALSE;
	}
}