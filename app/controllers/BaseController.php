<?php

use Negotiation\FormatNegotiator;

class BaseController extends Controller {

	protected $format;

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	public function preferredFormat()
	{
		if (is_null($this->format)) {
			$negotiator = new FormatNegotiator;
			$acceptHeader = $_SERVER['HTTP_ACCEPT'];

			$priorities = array('text/html', 'application/json');
			$this->format = $negotiator->getBest($acceptHeader, $priorities)->getValue();
		}
		return $this->format;
	}

}