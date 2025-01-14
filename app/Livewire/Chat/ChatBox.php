<?php

namespace App\Livewire\Chat;

use App\Models\Message;
use Livewire\Component;

class ChatBox extends Component
{


    public $selectedConversation ;
    public $body ;
    public $loadedMessages ;
    public  $pagainateVar = 10;


    protected $listeners = ['loadMore' , 'update-chat-height'];

    public function loadMore()
    {
        //dd('detected');
         $this->pagainateVar += 10;

         $this->loadMessages();

         $this->dispatch('update-chat-height');

    }


    public function loadMessages()
    {

        //get count
        $count = Message::where('conversation_id' , $this->selectedConversation->id )->count();


        //skip and query
        $this->loadedMessages = Message::where('conversation_id' , $this->selectedConversation->id )
            ->skip($count - $this->pagainateVar )   //
            ->take($this->pagainateVar)
            ->get();

       return $this->loadedMessages;

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
