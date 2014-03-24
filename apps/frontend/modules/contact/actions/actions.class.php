<?php

/**
 * contact actions.
 *
 * @package    address_book
 * @subpackage contact
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class contactActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->contacts = Doctrine_Core::getTable('Contact')
      ->createQuery('a')
      ->execute();    
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->contact = Doctrine_Core::getTable('Contact')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->contact);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new ContactForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new ContactForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($contact = Doctrine_Core::getTable('Contact')->find(array($request->getParameter('id'))), sprintf('Object contact does not exist (%s).', $request->getParameter('id')));
    $this->form = new ContactForm($contact);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($contact = Doctrine_Core::getTable('Contact')->find(array($request->getParameter('id'))), sprintf('Object contact does not exist (%s).', $request->getParameter('id')));
    $this->form = new ContactForm($contact);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($contact = Doctrine_Core::getTable('Contact')->find(array($request->getParameter('id'))), sprintf('Object contact does not exist (%s).', $request->getParameter('id')));
    $contact->delete();

    $this->redirect('contact/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $contact = $form->save();

      $this->redirect('contact/edit?id='.$contact->getId());
    }
  }
}
