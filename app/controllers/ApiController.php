<?php


class ApiController extends BaseController {

	/**
	 * SNL API request
	 *
	 * @return Response
	 */
	public function getSnl()
	{
		$q = Input::get('query');
		return Response::JSON(json_decode(file_get_contents('https://snl.no/api/v1/search?query=' . urlencode($q))));
	}

	/**
	 * Primo API request
	 *
	 * @return Response
	 */
	public function getPrimo()
	{
		$f = Input::get('field');
		$q = Input::get('query');
		$response = file_get_contents('http://bibsys-primo.hosted.exlibrisgroup.com/PrimoWebServices/xservice/search/brief?institution=UBO&json=true&indx=1&bulkSize=20&onCampus=false&lang=nor&sortField=scdate&loc=local,scope:(BIBSYS_ILS)&query=' . urlencode($f . ',exact,' . $q));
		$docs = $response->results->SEGMENTS->JAGROOT->RESULT->DOCSET->DOC;
		$out = array();
		foreach ($docs as $doc) {
			$out[] = array(
				'title' => $doc->PrimoNMBib->record->addata->btitle,
			);
		}
		return Response::JSON($out);
	}

}
