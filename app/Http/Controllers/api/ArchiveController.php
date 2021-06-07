<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Customs\Messages;

use App\Models\ForReferral;
use App\Models\CommitteeReport;
use App\Models\SecondReading;
use App\Models\ThirdReading;
use App\Models\Endorsement;
use App\Models\Ordinance;
use App\Models\Resolution;
use App\Models\Appropriation;

use App\Http\Resources\ForReferral\ForReferralListResourceCollection;
use App\Http\Resources\CommitteeReport\CommitteeReportListResourceCollection;
use App\Http\Resources\SecondReading\SecondReadingListResourceCollection;
use App\Http\Resources\ThirdReading\ThirdReadingListResourceCollection;
use App\Http\Resources\Endorsement\EndorsementListResourceCollection;
use App\Http\Resources\Ordinance\OrdinanceListResourceCollection;
use App\Http\Resources\Resolution\ResolutionListResourceCollection;
use App\Http\Resources\Appropriation\AppropriationListResourceCollection;


class ArchiveController extends Controller
{
    use Messages;

    private $http_code_ok;
    private $http_code_error;    

	public function __construct()
	{
		$this->middleware(['auth:api']);
		
        $this->http_code_ok = 200;
        $this->http_code_error = 500;

	}

    //for_referral
    public function archiveForReferral($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = ForReferral::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => true,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    public function restoreForReferral($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = ForReferral::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => false,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    //committee report
    public function archiveCommitteeReport($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = CommitteeReport::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => true,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    public function restoreCommitteeReport($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = CommiteeReport::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => false,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    //second reading
    public function archiveSecondReading($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = SecondReading::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => true,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    public function restoreSecondReading($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = SecondReading::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => false,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    //third reading
    public function archiveThirdReading($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = ThirdReading::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => true,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    public function restoreThirdReading($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = ThirdReading::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => false,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    //endorsement
    public function archiveEndorsement($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = Endorsement::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => true,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    public function restoreEndorsement($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = Endorsement::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => false,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    //Resolution
    public function archiveResolution($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = Resolution::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => true,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    public function restoreResolution($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = Resolution::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => false,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    //Ordinance
    public function archiveOrdinance($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = Ordinance::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => true,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    public function restoreOrdinance($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = Ordinance::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => false,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    //Appropriation
    public function archiveAppropriation($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = Appropriation::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => true,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    public function restoreAppropriation($id) {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $archive = Appropriation::find($id);

        if (is_null($archive)) {
			return $this->jsonErrorResourceNotFound();
        }

        $archive->fill([
            'archive' => false,
        ]);

        $archive->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Archived");
    }

    //Archive LISTS
    public function forReferrals() {

        $archive = ForReferral::where('archive',1)->orderBy('id','desc')->paginate(10);

        $data = new ForReferralListResourceCollection($archive);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function committeeReports() {

        $archive = CommitteeReport::where('archive',1)->orderBy('id','desc')->paginate(10);

        $data = new CommitteeReportListResourceCollection($archive);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function secondReadings() {

        $archive = SecondReading::where('archive',1)->orderBy('id','desc')->paginate(10);

        $data = new SecondReadingListResourceCollection($archive);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function thirdReadings() {

        $archive = ThirdReading::where('archive',1)->orderBy('id','desc')->paginate(10);

        $data = new ThirdReadingListResourceCollection($archive);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function endorsements() {

        $archive = Endorsement::where('archive',1)->orderBy('id','desc')->paginate(10);

        $data = new EndorsementListResourceCollection($archive);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function resolutions() {

        $archive = Resolution::where('archive',1)->orderBy('resolution_no','desc')->paginate(10);

        $data = new ResolutionListResourceCollection($archive);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function ordinances() {

        $archive = Ordinance::where('archive',1)->orderBy('ordinance_no','desc')->paginate(10);

        $data = new OrdinanceListResourceCollection($archive);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function appropriations() {

        $archive = Appropriation::where('archive',1)->orderBy('appropriation_no','desc')->paginate(10);

        $data = new AppropriationListResourceCollection($archive);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }
}
