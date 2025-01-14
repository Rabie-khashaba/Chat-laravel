<?php

namespace App\Livewire\Chat;

use App\Models\Message;
use Livewire\Component;

class ChatBox extends Component
{


    public $selectedConversation ;
    public $body ;
    public $loadedMessages ;





    public function loadMessages()
    {
        $this->loadedMessages = Message::where('conversation_id' , $this->selectedConversation->id )->get();

        //dd($this->loadedMessages);


    }
    public  function sendMessage()
    {

        $this->validate(['body' => 'required|string']);

        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'body' => $this->body

        ]);
        //reset input
        $this->reset("body");
//        dd($createdMessage);

        // scroll to bottom
        $this->dispatch('scroll-bottom');

        // push message
        $this->loadedMessages->push($createdMessage);

        #refresh chatlist
        $this->dispatch('refresh');    // emit not work in livewire 3

        //Update conversation
        $this->selectedConversation->updated_at = now() ;
        $this->selectedConversation->save();



    }




    public function mount()
    {
        $this->loadMessages();
    }
    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
