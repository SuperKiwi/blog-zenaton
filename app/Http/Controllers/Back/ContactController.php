<?php

namespace Framework\Http\Controllers\Back;

use Framework\ {
    Models\Contact,
    Repositories\ContactRepository,
    Http\Controllers\Controller
};

class ContactController extends Controller
{
    use Indexable;

    /**
     * Create a new ContactController instance.
     *
     * @param  \Framework\Repositories\ContactRepository $repository
     */
    public function __construct(ContactRepository $repository)
    {
        $this->repository = $repository;

        $this->table = 'contacts';
    }

    /**
     * Update "new" field for contact.
     *
     * @param  \Framework\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function updateSeen(Contact $contact)
    {
        $contact->ingoing->delete ();

        return response ()->json ();
    }

    /**
     * Remove contact from storage.
     *
     * @param  \Framework\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        $contact->delete ();

        return response ()->json ();
    }
}
