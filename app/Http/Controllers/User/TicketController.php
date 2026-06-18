<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User;

use MUCRM\Engine\Support\Auth\Auth;
use MUCRM\Engine\Support\{RateLimiter, Request};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Ticket;

class TicketController extends Controller
{
    protected string $layout = "components.layouts.app";

    public function index()
    {
        $tickets_open = auth()->tickets()
            ->where('status', 'open')
            ->orderBy('updated_at', 'DESC')
            ->withCount('messages')
            ->get();

        $tickets_answered = auth()->tickets()
            ->where('status', 'answered')
            ->orderBy('updated_at', 'DESC')
            ->withCount('messages')
            ->get();

        $tickets_closed = auth()->tickets()
            ->where('status', 'closed')
            ->orderBy('updated_at', 'DESC')
            ->withCount('messages')
            ->get();

        return $this->view("user.ticket.index", compact('tickets_open', 'tickets_answered', 'tickets_closed'))->title(__lang('support.title'));
    }

    public function create()
    {
        return $this->view("user.ticket.create")->title(__lang('support.title'));
    }

    public function store(Request $request)
    {
        $rateLimiter = new RateLimiter();

        $key = 'ticket_attempts:' . $request->ip();

        if ($rateLimiter->tooManyAttempts($key, 1)) {
            $segundos = $rateLimiter->availableIn($key);

            $tempoMsg = $segundos >= 60 ? floor($segundos / 60) . " " . __lang('user.msg.minutes') : $segundos . " " . __lang('user.msg.seconds');

            return $request->messageError(__lang('user.msg.try_again_in', ['time' => $tempoMsg]))->back();
        }

        $request->validate([
            'department' => 'required|string|in:financial,technical,bugs,complaints',
            'subject'    => 'required|string|max:150',
            'message'    => 'required|string|max:5000',
        ]);

        $ticket = Ticket::create([
            'memb___id'  => Auth::user()->memb___id,
            'subject'    => $request->subject,
            'department' => $request->department,
            'status'     => 'open',
        ]);

        $ticket->messages()->create([
            'sender_id'   => Auth::user()->memb___id,
            'sender_type' => 'player',
            'message'     => nl2br($request->message),
        ]);

        $rateLimiter->hit($key, 10);

        return $request->message('success', __lang('support.ticket_created'))->back('user.ticket.index');
    }

    public function show(Request $request, Ticket $ticket)
    {
        if ($ticket->memb___id !== Auth::user()->memb___id) {
            return $request->message('error', __lang('support.messages.ticket_not_found'))->route('user.ticket.index');
        }

        $messages = $ticket->messages()->orderBy('created_at', 'ASC')->get();

        return $this->view("user.ticket.show", compact('ticket', 'messages'))->title($ticket->subject);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        if ($ticket->memb___id !== Auth::user()->memb___id) {
            return $request->messageError(__lang('support.messages.ticket_not_found'))->route('user.tickets.index');
        }

        if ($ticket->status == 'closed') {
            return $request->messageError(__lang('support.messages.ticket_closed'))->back('user.tickets.show', ['ticket' => $ticket->id]);
        }

        $rateLimiter = new RateLimiter();

        $key = "reply_ticket_attempts:" . $ticket->id . ":" . $request->ip();

        if ($rateLimiter->tooManyAttempts($key, 1)) {
            $segundos = $rateLimiter->availableIn($key);
            $tempoMsg = $segundos >= 60 ? floor($segundos / 60) . " " . __lang('user.msg.minutes') : $segundos . " " . __lang('user.msg.seconds');

            return $request->messageError(__lang('user.msg.try_again_in', ['time' => $tempoMsg]))->back();
        }

        $request->validate([
            'message' => 'required|string|max:5000',
        ], [
            'message.required' => __lang('support.validation.message_required'),
            'message.string'   => __lang('support.validation.message_string'),
            'message.max'      => __lang('support.validation.message_max'),
        ]);

        $ticket->messages()->create([
            'sender_id'   => Auth::user()->memb___id,
            'sender_type' => 'player',
            'message'     => nl2br($request->message),
        ]);

        $rateLimiter->hit($key, 10);

        return $request->message(__lang('support.messages.ticket_answered'))->back('user.tickets.show', ['ticket' => $ticket->id]);
    }
}
