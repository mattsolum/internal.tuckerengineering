<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Asset extends CI_Model
{
	private $CI = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->helper('structures/asset');
	}

	public function commit($asset, $type, $type_id)
	{
		if($asset->is_valid() == FALSE)
		{
			return FALSE;
		}

		if($this->exists($asset))
		{
			return $this->update($asset, $type, $type_id);
		}
		else
		{
			return $this->create($asset, $type, $type_id);
		}
	}

	private function create($asset, $type, $type_id)
	{
		$this->CI->db->trans_start();

		$data = array();
		$link = array();

		$id = $this->get_next_id();

		$data['asset_id'] 		= $id;
		$data['filename'] 		= $asset->filename;
		$data['date_added'] 	= now();
		$data['date_updated'] 	= now();

		$this->CI->db->insert('assets', $data);

		$link['asset_id'] 		= $id;
		$link['id'] 			= $type_id;
		$link['type'] 			= $type;

		$this->CI->db->insert('job_assets', $link);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Asset committing object.');
			return FALSE;
		}
		else return TRUE;
	}

	private function update($asset, $type, $type_id)
	{
		$this->CI->db->trans_start();

		//What is to update?

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Asset committing object.');
			return FALSE;
		}
		else return TRUE;
	}

	public function delete($asset_id)
	{
		$this->CI->db->trans_start();

		$where = array('asset_id' => $asset_id);
		$this->CI->db->delete('assets', $where);
		$this->CI->db->delete('job_assets', $where);

		$this->CI->db->trans_complete();
		
		if($this->CI->db->trans_status() === FALSE)
		{
			log_message('error', 'Error in model Asset committing object.');
			return FALSE;
		}
		else return TRUE;
	}

	public function get($asset_id)
	{
		$where = array('asset_id' => $asset_id);

		$query = $this->CI->db->get_where('assets', $where);

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			$asset = new StructAsset();

			$asset->id 				= $row->asset_id;
			$asset->filename 		= $row->filename;
			$asset->date_added 		= $row->date_added;
			$asset->date_updated 	= $row->date_updated;

			return $asset;
		}

		return false;
	}

	public function get_by_job($job_id)
	{
		$results = array();
		$where = array('job_id' => $job_id);

		$list = $this->CI->db->get_where('job_assets', $where);

		if($list->num_rows() > 0)
		{
			foreach($list->result() AS $item)
			{
				$results[] = $this->get($item->asset_id);
			}
		}

		return $results;
	}

	private function get_by_type($id, $type)
	{

	}

	private function get_next_id()
	{
		$this->CI->db->order_by('asset_id', 'DESC');
		$this->CI->db->limit(1);
		$query = $this->CI->db->get('assets');

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			return $row->asset_id + 1;
		}

		return 1;
	}

	private function exists($asset)
	{
		$where = array();

		$where['filename'] = $asset->filename;

		$query = $this->CI->db->get_where('assets', $where);

		if($query->num_rows() > 0)
		{
			$row = $query->row(0);

			return $row->asset_id;
		}
	}
}