@extends('titan::layouts.admin')

@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">
						<span><i class="fa fa-table"></i></span>
						<span>List All Testimonials</span>
					</h3>
				</div>

				<div class="box-body">

					@include('titan::admin.partials.info')

					<div class="well well-sm well-toolbar">
						<div class="row">
							<div class="col-sm-6">
								@include('titan::admin.partials.toolbar', ['order' => true])
							</div>
							<div class="col-sm-6 text-right">
								<div class="btn-group">
									<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fa fa-download"></i> Export <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="{{ url('admin/testimonials/order/export?format=csv') }}"><i class="fa fa-file-csv"></i> Export as CSV</a></li>
										<li><a href="{{ url('admin/testimonials/order/export?format=json') }}"><i class="fa fa-file-code"></i> Export as JSON</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<table id="tbl-list" data-server="false" class="dt-table table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Customer</th>
                            <th class="desktop" style="width: 50%">Testimonial</th>
                            <th>Link</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->customer }}</td>
                                <td>{!! $item->description !!}</td>
                                <td><a href="{{ $item->link }}" target="_blank">{{ $item->link }}</a></td>
                                <td>{{ format_date($item->created_at) }}</td>
                                <td>{!! action_row($selectedNavigation->url, $item->id, $item->title, ['show', 'edit', 'delete']) !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection