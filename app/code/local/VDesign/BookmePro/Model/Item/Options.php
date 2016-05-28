<?php


class VDesign_BookmePro_Model_Item_Options
{
	
	public function getTimes(){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$collection = Mage::getModel('bookmepro/session')->getCollection();
		
		$collection
		->getSessionsOnly()
		->groupByBookTime();
		
		$out = array();
		foreach ($collection as $item)
		{
			$out [$item->getTimeFrom()] = $item->getTimeFrom();
		}
		return $out;
	}
	
	public function getAdvStartTimes(){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$collection = Mage::getModel('bookmepro/session')->getCollection();
		
		$collection
		->getAdventuresOnly()
		->groupByBookTime();
		
		$out = array();
		foreach ($collection as $item)
		{
			$out [$item->getTimeFrom()] = $item->getTimeFrom();
		}
		return $out;
	}
	
	public function getAdvEndTimes(){
		date_default_timezone_set(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		$collection = Mage::getModel('bookmepro/session')->getCollection();
	
		$collection
		->getAdventuresOnly()
		->groupByBookToTime();
		
		$out = array();
		foreach ($collection as $item)
		{
			$out [$item->getTimeTo()] = $item->getTimeTo();
		}
		
		return $out;
	}
	
}