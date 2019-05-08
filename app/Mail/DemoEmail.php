<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
class DemoEmail extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $demo;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($demo,$attach="")
    {
        $this->demo = $demo;
        $this->attach = $attach;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(!empty($this->attach)){
        return $this->from('sender@example.com')
                    ->subject($this->demo->subject)
                    ->attach($this->attach)
                    ->view('mails.demo')
                    ->text('mails.demo_plain');
                   
        }
        else{
            
            return $this->from('sender@example.com')
                    ->subject($this->demo->subject)
                    ->view('mails.demo')
                    ->text('mails.demo_plain');
        }        
                      
    }
}