<?php

class Debit extends SQLModel
{
	protected static $classname;
	protected static $table = 'debits';
	
	const STATE_NEW = 0;
	const STATE_SCHEDULED = 1;
	const STATE_PENDING = 2;
	const STATE_COMPLETED = 3;
	const STATE_FAILED = 4;
	const STATE_CANCELED = 5;
	
	public function get_state_text()
	{
		switch ($this->state)
		{
			case self::STATE_NEW:
				return 'new';
			case self::STATE_SCHEDULED:
				return 'scheduled';
			case self::STATE_PENDING:
				return 'pending';
			case self::STATE_COMPLETED:
				return 'completed';
			case self::STATE_FAILED:
				return 'failed';
			case self::STATE_CANCELED:
				return 'canceled';
		}
		
		return '';
	}
	
	protected function create()
	{
		$this->id = 'NULL';
		$this->created_at = date('Y-m-d H:i:s');
		if (!$this->state)
		{
			$this->state = self::STATE_NEW;
		}
		
		return parent::create();
	}
	protected function update()
	{
		$this->modified_at = date('Y-m-d H:i:s');
		
		return parent::update();
	}
	
	public function destroy()
	{
		Payment::delete(array('where' => array('debit_id = $1', $this->id)));
		
		return parent::destroy();
	}
}