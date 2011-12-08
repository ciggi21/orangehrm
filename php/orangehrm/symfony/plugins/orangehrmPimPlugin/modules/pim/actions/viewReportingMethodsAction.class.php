<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

class viewReportingMethodsAction extends sfAction {
    
    private $reportingMethodService;
    
    public function getReportingMethodService() {
        
        if (!($this->reportingMethodService instanceof ReportingMethodService)) {
            $this->reportingMethodService = new ReportingMethodService();
        }        
        
        return $this->reportingMethodService;
    }

    public function setReportingMethodService($reportingMethodService) {
        $this->reportingMethodService = $reportingMethodService;
    }
    
    public function execute($request) {
        
        $this->_checkAuthentication();
        
        $this->form = new ReportingMethodForm();
        $this->records = $this->getReportingMethodService()->getReportingMethodList();
        
		if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
        }        
        
        if ($request->isMethod('post')) {
            
			$this->form->bind($request->getParameter($this->form->getName()));
            
			if ($this->form->isValid()) {

                $this->_checkDuplicateEntry();
                
				$templateMessage = $this->form->save();
				$this->getUser()->setFlash('templateMessage', $templateMessage);                
                $this->redirect('pim/viewReportingMethods');
                
            }
            
        }
        
    }
    
    protected function _checkAuthentication() {
        
        $user = $this->getUser()->getAttribute('user');
        
		if (!$user->isAdmin()) {
			$this->redirect('pim/viewPersonalDetails');
		}
        
    }

    protected function _checkDuplicateEntry() {

        $id = $this->form->getValue('id');

        if (empty($id) && $this->getReportingMethodService()->isExistingReportingMethodName($this->form->getValue('name'))) {
            $this->getUser()->setFlash('templateMessage', array('WARNING', __('ReportingMethod Name Exists')));
            $this->redirect('pim/viewReportingMethods');
        }

    }
    
}
