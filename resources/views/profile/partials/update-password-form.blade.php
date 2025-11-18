<section>
    
    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="card-box height-100-p overflow-hidden">
							<div class="profile-tab height-100-p">
								<div class="tab height-100-p">
									<ul class="nav nav-tabs customtab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#timeline" role="tab">Update Details</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#setting" role="tab"></a>
										</li>
									</ul>
									<div class="tab-content">
										<!-- Timeline Tab start -->
										<div class="tab-pane fade show active" id="timeline" role="tabpanel">
											<div class="pd-20">
												<div class="profile-timeline">
												<form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                                    @csrf
                                                    @method('put')
													<div class="profile-edit-list row">
														<div class="col-md-12"><h4 class="text-blue h5 mb-20">{{ __('Update Password') }}</h4></div>
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Current Password</label>
																<input id="update_password_current_password" name="current_password" type="password" class="form-control form-control-lg" autocomplete="current-password">
                                                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
															</div>
														</div>
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>New Password</label>
																<div class="input-group">
																	<input id="update_password_password" name="password" type="password" class="form-control form-control-lg" autocomplete="new-password">
																	<x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
																</div>
															</div>
														</div>
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Confirm New Password</label>
																<div class="input-group">
																	<input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control form-control-lg" autocomplete="new-password">
                                                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
																	
																</div>
															</div>
														</div>
                                                        <div class="flex items-center gap-4">
                                                            <button class="btn btn-primary">{{ __('Save') }}</button>
                                                            @if (session('status') === 'password-updated')
                                                            <p
                                                            x-data="{ show: true }"
                                                            x-show="show"
                                                            x-transition
                                                            x-init="setTimeout(() => show = false, 2000)"
                                                            class="text-sm text-gray-600"
                                                            >{{ __('Saved.') }}</p>
                                                            @endif
                                                        </div>
														
														
														
													</div>
												</form>
													
												
												</div>
											</div>
										</div>
										
										<!-- Setting Tab End -->
									</div>
								</div>
							</div>
						</div>
					</div>
</section>
