<?php 

namespace App\Notification;

use Twig\Environment;
use App\Entity\Contact;

class ContactNotification
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer;

  /**
   * @var Environment
   */
  private $renderer;

  // hors d'un controller, on peut faire des injections de dépendance seulement dans un constructeur 

  public function __construct(\Swift_Mailer $mailer, Environment $renderer )
  {
    $this->mailer = $mailer;
    $this->renderer = $renderer;
  }

  public function notify(Contact $contact)
  {
    $message = (new \Swift_Message('Nouveau message de :' . $contact->getEmail())) 
    //Permet d'afficher le sujet du message à la réception du mail

    ->setFrom($contact->getEmail()) 
    // expéditeur du mail 

    ->setTo("zaza@mail.com") 
    // Destinataire du mail

    ->setReplyTo($contact->getEmail()) 
    // addresse de réponse du mail 

    ->setBody($this->renderer->render("email/contact.html.twig", [ // corps du message
        'contact' => $contact
    ]), 'text/html');

    // le corps du message sera contenu dans un template 
    // Nous devons préciser que le mail sera en format html pour pouvoir interpréter les balises 
    $this->mailer->send($message); // envoi du mail 
}
}