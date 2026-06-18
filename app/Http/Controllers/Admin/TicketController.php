<?php

namespace MUCRM\Http\Controllers\Admin;

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Ticket;

class TicketController extends Controller
{
    protected string $layout = "panels.admin.components.layouts.app";

    public function index(Request $request)
    {
        $statusFilter = $request->input('status', 'pending');
        $searchFilter = $request->input('search');

        $query = Ticket::query()->orderBy('updated_at', 'DESC')->withCount('messages');

        if ($statusFilter === 'open') {
            $query->where('status', 'open');
        } elseif ($statusFilter === 'answered') {
            $query->where('status', 'answered');
        } elseif ($statusFilter === 'closed') {
            $query->where('status', 'closed');
        } else {
            $query->whereIn('status', ['open', 'answered']);
        }

        if ($searchFilter) {
            $query->where('subject', 'like', "%$searchFilter%")->orWhere('memb___id', 'like', "%$searchFilter%");
        }

        $tickets = $query->paginate(20);

        return $this->view("panels.admin.ticket.index", compact('tickets', 'statusFilter'))
            ->title(__lang('support.title'));
    }

    public function show(Ticket $ticket)
    {

        $messages = $ticket->messages()->orderBy('created_at', 'ASC')->get();

        return $this->view("panels.admin.ticket.show", compact('ticket', 'messages'))->title($ticket->subject);
    }

    public function reply(Request $request, Ticket $ticket)
    {

        $request->validate([
            'message' => 'required|string|max:5000',
        ], [
            'message.required' => __lang('support.validation.message_required'),
            'message.string'   => __lang('support.validation.message_string'),
            'message.max'      => __lang('support.validation.message_max'),
        ]);

        $ticket->update([
            'status' => 'answered',
        ]);

        $ticket->messages()->create([
            'sender_id'   => 'staff',
            'sender_type' => 'staff',
            'message'     => nl2br($request->message),
        ]);

        return $request->message(__lang('support.messages.ticket_answered'))->back('admin.tickets.show', ['ticket' => $ticket->id]);
    }

    public function delete(Request $request, Ticket $ticket)
    {
        $ticket->messages()->delete();
        $ticket->delete();

        return $request->message(__lang('support.messages.ticket_deleted'))->back('admin.tickets.index');
    }

    public function close(Request $request, Ticket $ticket)
    {

        $ticket->update([
            'status' => 'closed',
        ]);

        return $request->message(__lang('support.messages.ticket_closed'))->back('admin.tickets.show', ['ticket' => $ticket->id]);
    }
}
