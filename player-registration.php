<?php
// player-registration.php
$pageTitle = 'UPPHL Season 2 — Player Trials Registration';
$extraCss = array (
  0 => 'assets/css/player-registration.css',
);
require_once __DIR__ . '/includes/header.php';

$pb = upphl_page_banner('player-registration');
$heroBg = !empty($pb['bannerImage']) ? $pb['bannerImage'] : '';
$heroStyle = !empty($heroBg) ? "background: linear-gradient(90deg, rgba(12,12,12,0.86), rgba(12,12,12,0.58), rgba(244,123,32,0.22)), url('" . htmlspecialchars($heroBg) . "') center/cover no-repeat;" : '';
?>

<section class="hero" <?= $heroStyle ? 'style="' . $heroStyle . '"' : '' ?>>
      <svg
        class="court"
        viewBox="0 0 1200 380"
        preserveAspectRatio="xMidYMid slice"
        aria-hidden="true">
        <g fill="none" stroke="rgba(255,255,255,.22)" stroke-width="2">
          <path d="M0 190 H1200" stroke-dasharray="10 12" />
          <path
            d="M-60 40 A230 230 0 0 1 -60 340"
            stroke="rgba(255,255,255,.3)" />
          <path d="M-60 -20 A330 330 0 0 1 -60 400" stroke-dasharray="8 10" />
          <path
            d="M1260 40 A230 230 0 0 0 1260 340"
            stroke="rgba(255,255,255,.3)" />
          <path d="M1260 -20 A330 330 0 0 0 1260 400" stroke-dasharray="8 10" />
          <circle cx="600" cy="190" r="86" stroke="rgba(255,106,19,.5)" />
        </g>
      </svg>
      <div class="hero-in">
        <div class="badge-row">
          <span class="chip solid">Season 2</span>
          <span class="chip">Single Phase Trials</span>
          <span class="chip">Registration Open</span>
        </div>
        <h1>UP Pro Handball League<span>Player Trials Registration</span></h1>
        <p class="lede">
          Fill every detail exactly as it appears on your Aadhaar card. UPPHL
          officials verify all information and documents during player
          selection. / सभी जानकारी आधार कार्ड के अनुसार सही-सही भरें।
        </p>
        <div class="meta">
          <div><b>Format</b><i>Single phase trial</i></div>
          <div><b>Sections</b><i>8 + declaration</i></div>
          <div><b>Documents</b><i>Aadhaar + photo</i></div>
          <div><b>Time needed</b><i>About 8 minutes</i></div>
        </div>
      </div>
    </section>

    <div class="progress-bar">
      <div class="pb-in">
        <span class="pb-label">Form completed</span>
        <div class="pb-track"><div class="pb-fill" id="pbFill"></div></div>
        <span class="pb-label" id="pbPct">0%</span>
      </div>
    </div>

    <main class="wrap">
      <div class="grid" id="mainGrid">
        <!-- ============ FORM ============ -->
        <form id="regForm" novalidate>
          <!-- 1 -->
          <section class="card">
            <div class="card-head">
              <div class="num">01</div>
              <div>
                <h2>Player Basic Details</h2>
                <p>Name and date of birth must match your Aadhaar card</p>
              </div>
            </div>
            <div class="card-body">
              <div class="field">
                <label class="lbl" for="fullName"
                  >Full Name of Player<em>*</em></label
                >
                <div class="hint">Exactly as printed on Aadhaar card</div>
                <input
                  type="text"
                  id="fullName"
                  name="fullName"
                  data-req
                  placeholder="e.g. Rahul Kumar Yadav"
                  autocomplete="name" />
                <div class="msg">Enter your full name as per Aadhaar.</div>
              </div>
              <div class="row three">
                <div class="field">
                  <label class="lbl" for="dob">Date of Birth<em>*</em></label>
                  <input type="date" id="dob" name="dob" data-req />
                  <div class="msg">Select your date of birth.</div>
                </div>
                <div class="field">
                  <label class="lbl" for="age">Age</label>
                  <input
                    type="text"
                    id="age"
                    name="age"
                    readonly
                    placeholder="Auto" />
                </div>
                <div class="field">
                  <label class="lbl">Gender<em>*</em></label>
                  <div class="choices" data-req-group="gender">
                    <label class="pick"
                      ><input type="radio" name="gender" value="Male" /><span
                        >Male</span
                      ></label
                    >
                    <label class="pick"
                      ><input type="radio" name="gender" value="Female" /><span
                        >Female</span
                      ></label
                    >
                    <label class="pick"
                      ><input
                        type="radio"
                        name="gender"
                        value="Prefer not to say" /><span
                        >Prefer Not to Say</span
                      ></label
                    >
                  </div>
                  <div class="msg">Choose one option.</div>
                </div>
              </div>
            </div>
          </section>

          <!-- 2 -->
          <section class="card">
            <div class="card-head">
              <div class="num">02</div>
              <div>
                <h2>Physical &amp; Playing Details</h2>
                <p>Used for position assessment during trials</p>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="field">
                  <label class="lbl" for="height">Height (cm)<em>*</em></label>
                  <input
                    type="number"
                    id="height"
                    name="height"
                    data-req
                    min="120"
                    max="230"
                    placeholder="e.g. 178" />
                  <div class="msg">Enter height in centimetres (120–230).</div>
                </div>
                <div class="field">
                  <label class="lbl" for="weight">Weight (kg)<em>*</em></label>
                  <input
                    type="number"
                    id="weight"
                    name="weight"
                    data-req
                    min="30"
                    max="180"
                    placeholder="e.g. 72" />
                  <div class="msg">Enter weight in kilograms (30–180).</div>
                </div>
              </div>
              <div class="field">
                <label class="lbl">Dominant / Playing Hand<em>*</em></label>
                <div class="choices" data-req-group="hand">
                  <label class="pick warm"
                    ><input type="radio" name="hand" value="Right Hand" /><span
                      >Right Hand</span
                    ></label
                  >
                  <label class="pick warm"
                    ><input type="radio" name="hand" value="Left Hand" /><span
                      >Left Hand</span
                    ></label
                  >
                </div>
                <div class="msg">Choose your throwing hand.</div>
              </div>
              <div class="field">
                <label class="lbl">Primary Playing Position<em>*</em></label>
                <div class="choices grid7" data-req-group="pos1">
                  <label class="pick"
                    ><input type="radio" name="pos1" value="Goalkeeper" /><span
                      >Goalkeeper</span
                    ></label
                  >
                  <label class="pick"
                    ><input type="radio" name="pos1" value="Left Wing" /><span
                      >Left Wing</span
                    ></label
                  >
                  <label class="pick"
                    ><input type="radio" name="pos1" value="Right Wing" /><span
                      >Right Wing</span
                    ></label
                  >
                  <label class="pick"
                    ><input type="radio" name="pos1" value="Left Back" /><span
                      >Left Back</span
                    ></label
                  >
                  <label class="pick"
                    ><input type="radio" name="pos1" value="Right Back" /><span
                      >Right Back</span
                    ></label
                  >
                  <label class="pick"
                    ><input
                      type="radio"
                      name="pos1"
                      value="Centre Back / Playmaker" /><span
                      >Centre Back</span
                    ></label
                  >
                  <label class="pick"
                    ><input
                      type="radio"
                      name="pos1"
                      value="Pivot / Line Player" /><span
                      >Pivot / Line</span
                    ></label
                  >
                </div>
                <div class="msg">Select your main position.</div>
              </div>
              <div class="field">
                <label class="lbl" for="pos2">Secondary Playing Position</label>
                <select id="pos2" name="pos2">
                  <option value="None">None</option>
                  <option>Goalkeeper</option>
                  <option>Left Wing</option>
                  <option>Right Wing</option>
                  <option>Left Back</option>
                  <option>Right Back</option>
                  <option>Centre Back / Playmaker</option>
                  <option>Pivot / Line Player</option>
                </select>
              </div>
            </div>
          </section>

          <!-- 3 -->
          <section class="card">
            <div class="card-head">
              <div class="num">03</div>
              <div>
                <h2>UPPHL Season 1 Participation</h2>
                <p>Returning players are tracked separately from new entries</p>
              </div>
            </div>
            <div class="card-body">
              <div class="field">
                <label class="lbl"
                  >Did You Play in UPPHL Season 1?<em>*</em></label
                >
                <div class="hint">क्या आपने UPPHL सीज़न 1 में खेला था?</div>
                <div class="choices" data-req-group="season1">
                  <label class="pick warm"
                    ><input
                      type="radio"
                      name="season1"
                      value="Yes"
                      data-cond="s1box" /><span
                      >Yes, I Played Season 1</span
                    ></label
                  >
                  <label class="pick warm"
                    ><input
                      type="radio"
                      name="season1"
                      value="No"
                      data-cond="" /><span>No, First Time</span></label
                  >
                </div>
                <div class="msg">Select yes or no.</div>
              </div>
              <div class="cond" id="s1box">
                <div class="row">
                  <div class="field">
                    <label class="lbl" for="s1team"
                      >Season 1 Team / Franchise</label
                    >
                    <input
                      type="text"
                      id="s1team"
                      name="s1team"
                      placeholder="Team you represented in Season 1" />
                  </div>
                  <div class="field">
                    <label class="lbl" for="s1matches"
                      >Matches Played in Season 1</label
                    >
                    <input
                      type="number"
                      id="s1matches"
                      name="s1matches"
                      min="0"
                      max="60"
                      placeholder="e.g. 8" />
                  </div>
                </div>
                <div class="field">
                  <label class="lbl" for="s1note"
                    >Season 1 Role or Performance Highlight</label
                  >
                  <textarea
                    id="s1note"
                    name="s1note"
                    placeholder="Position played, goals scored, awards received in Season 1"></textarea>
                </div>
              </div>
            </div>
          </section>

          <!-- 4 -->
          <section class="card">
            <div class="card-head">
              <div class="num">04</div>
              <div>
                <h2>Handball Experience &amp; Achievements</h2>
                <p>
                  Mention tournament name, team or state represented, and year
                </p>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="field">
                  <label class="lbl" for="exp"
                    >Total Playing Experience<em>*</em></label
                  >
                  <input
                    type="text"
                    id="exp"
                    name="exp"
                    data-req
                    placeholder="e.g. 5 years" />
                  <div class="msg">Enter your total handball experience.</div>
                </div>
                <div class="field">
                  <label class="lbl" for="level"
                    >Highest Level Represented<em>*</em></label
                  >
                  <select id="level" name="level" data-req>
                    <option value="">Select level</option>
                    <option>School Level</option>
                    <option>District Level</option>
                    <option>State Level</option>
                    <option>National Level</option>
                    <option>University Level</option>
                    <option>All India University</option>
                    <option>Department / Services Level</option>
                    <option>Professional League</option>
                    <option>International Level</option>
                    <option>Other</option>
                  </select>
                  <div class="msg">
                    Select the highest level you have played.
                  </div>
                </div>
              </div>
              <div class="field">
                <label class="lbl" for="club"
                  >Current Club / Academy / Team</label
                >
                <input
                  type="text"
                  id="club"
                  name="club"
                  placeholder="If applicable" />
              </div>
              <div class="field">
                <label class="lbl" for="tournaments"
                  >Major Tournaments / Championships / Leagues Played</label
                >
                <textarea
                  id="tournaments"
                  name="tournaments"
                  placeholder="Senior National Championship — Uttar Pradesh — 2024&#10;All India University Games — 2023"></textarea>
              </div>
              <div class="field">
                <label class="lbl" for="achieve"
                  >Major Achievements — Last 3 Years</label
                >
                <textarea
                  id="achieve"
                  name="achieve"
                  placeholder="Gold medal, State Senior Championship, 2025"></textarea>
              </div>
              <div class="field">
                <label class="lbl">Department Representation<em>*</em></label>
                <div class="choices" data-req-group="dept">
                  <label class="pick"
                    ><input
                      type="radio"
                      name="dept"
                      value="Not associated with any department"
                      data-cond="" /><span>Not Associated</span></label
                  >
                  <label class="pick"
                    ><input
                      type="radio"
                      name="dept"
                      value="Yes, currently representing a department"
                      data-cond="deptbox" /><span
                      >Representing a Department</span
                    ></label
                  >
                </div>
                <div class="msg">Select one option.</div>
              </div>
              <div class="cond" id="deptbox">
                <div class="field">
                  <label class="lbl" for="deptName">Department Name</label>
                  <input
                    type="text"
                    id="deptName"
                    name="deptName"
                    placeholder="Indian Railways / Services / Police / Army / PSU / Other" />
                  <div class="hint">
                    A player representing a department may need to submit an NOC
                    if UPPHL requests it.
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- 5 -->
          <section class="card">
            <div class="card-head">
              <div class="num">05</div>
              <div>
                <h2>Medical &amp; Injury Information</h2>
                <p>
                  Disclosing an injury does not disqualify you — it is collected
                  for player safety
                </p>
              </div>
            </div>
            <div class="card-body">
              <div class="field">
                <label class="lbl">Current Fitness Status<em>*</em></label>
                <div class="choices" data-req-group="fitness">
                  <label class="pick"
                    ><input
                      type="radio"
                      name="fitness"
                      value="Fully Fit" /><span>Fully Fit</span></label
                  >
                  <label class="pick"
                    ><input
                      type="radio"
                      name="fitness"
                      value="Recovering from Injury" /><span
                      >Recovering from Injury</span
                    ></label
                  >
                  <label class="pick"
                    ><input
                      type="radio"
                      name="fitness"
                      value="Currently Injured" /><span
                      >Currently Injured</span
                    ></label
                  >
                </div>
                <div class="msg">Select your current fitness status.</div>
              </div>
              <div class="field">
                <label class="lbl">Injury History<em>*</em></label>
                <div class="choices grid7" data-req-group="injury">
                  <label class="pick"
                    ><input
                      type="radio"
                      name="injury"
                      value="No major injury till date"
                      data-cond="" /><span>No Major Injury</span></label
                  >
                  <label class="pick"
                    ><input
                      type="radio"
                      name="injury"
                      value="Had an injury earlier and fully recovered"
                      data-cond="injbox" /><span
                      >Earlier, Fully Recovered</span
                    ></label
                  >
                  <label class="pick"
                    ><input
                      type="radio"
                      name="injury"
                      value="Currently recovering from an injury"
                      data-cond="injbox" /><span
                      >Currently Recovering</span
                    ></label
                  >
                  <label class="pick"
                    ><input
                      type="radio"
                      name="injury"
                      value="Currently injured"
                      data-cond="injbox" /><span>Currently Injured</span></label
                  >
                </div>
                <div class="msg">Select one option.</div>
              </div>
              <div class="cond" id="injbox">
                <div class="field">
                  <label class="lbl" for="injuryDetail">Injury Details</label>
                  <textarea
                    id="injuryDetail"
                    name="injuryDetail"
                    placeholder="Injury type, year or date, and current recovery status"></textarea>
                </div>
              </div>
            </div>
          </section>

          <!-- 6 -->
          <section class="card">
            <div class="card-head">
              <div class="num">06</div>
              <div>
                <h2>Residential Details</h2>
                <p>
                  Address is used for trial centre allotment and communication
                </p>
              </div>
            </div>
            <div class="card-body">
              <div class="field">
                <label class="lbl" for="address"
                  >Complete Residential Address with PIN Code<em>*</em></label
                >
                <textarea
                  id="address"
                  name="address"
                  data-req
                  placeholder="House / street / locality, city, PIN code"></textarea>
                <div class="msg">Enter your full address with PIN code.</div>
              </div>
              <div class="row">
                <div class="field">
                  <label class="lbl" for="district">District<em>*</em></label>
                  <input
                    type="text"
                    id="district"
                    name="district"
                    data-req
                    placeholder="e.g. Varanasi" />
                  <div class="msg">Enter your district.</div>
                </div>
                <div class="field">
                  <label class="lbl" for="state"
                    >State / Union Territory<em>*</em></label
                  >
                  <input
                    type="text"
                    id="state"
                    name="state"
                    data-req
                    value="Uttar Pradesh" />
                  <div class="msg">Enter your state.</div>
                </div>
              </div>
            </div>
          </section>

          <!-- 7 -->
          <section class="card">
            <div class="card-head">
              <div class="num">07</div>
              <div>
                <h2>Identity &amp; Documents</h2>
                <p>
                  Clear, readable images only — JPG, PNG or PDF up to 5 MB each
                </p>
              </div>
            </div>
            <div class="card-body">
              <div class="field">
                <label class="lbl" for="aadhaar"
                  >Aadhaar Card Number<em>*</em></label
                >
                <input
                  type="text"
                  id="aadhaar"
                  name="aadhaar"
                  data-req
                  inputmode="numeric"
                  maxlength="14"
                  placeholder="1234 5678 9012" />
                <div class="msg">Enter a valid 12-digit Aadhaar number.</div>
              </div>

              <div class="field">
                <label class="lbl"
                  >Recent Passport Size Photograph<em>*</em></label
                >
                <label class="up" id="upPhoto">
                  <span class="up-ico" id="photoIco">📷</span>
                  <span class="up-txt"
                    ><b>Upload Player Photograph</b
                    ><small
                      >Clear front-facing photo, plain background</small
                    ></span
                  >
                  <input
                    type="file"
                    id="photo"
                    name="photo"
                    accept="image/*"
                    data-req-file />
                </label>
                <div class="msg">Upload a recent passport size photograph.</div>
              </div>

              <div class="row">
                <div class="field">
                  <label class="lbl">Aadhaar Card — Front Side<em>*</em></label>
                  <label class="up" id="upAF">
                    <span class="up-ico">🪪</span>
                    <span class="up-txt"
                      ><b>Upload Front Side</b
                      ><small>No file chosen</small></span
                    >
                    <input
                      type="file"
                      id="aadhaarFront"
                      name="aadhaarFront"
                      accept="image/*,application/pdf"
                      data-req-file />
                  </label>
                  <div class="msg">
                    Upload the front side of your Aadhaar card.
                  </div>
                </div>
                <div class="field">
                  <label class="lbl">Aadhaar Card — Back Side<em>*</em></label>
                  <label class="up" id="upAB">
                    <span class="up-ico">🪪</span>
                    <span class="up-txt"
                      ><b>Upload Back Side</b
                      ><small>No file chosen</small></span
                    >
                    <input
                      type="file"
                      id="aadhaarBack"
                      name="aadhaarBack"
                      accept="image/*,application/pdf"
                      data-req-file />
                  </label>
                  <div class="msg">
                    Upload the back side of your Aadhaar card.
                  </div>
                </div>
              </div>

              <div class="field">
                <label class="lbl">Handball Achievement Certificates</label>
                <label class="up" id="upCert">
                  <span class="up-ico">🏅</span>
                  <span class="up-txt"
                    ><b>Upload Certificates (Optional)</b
                    ><small>Multiple files allowed</small></span
                  >
                  <input
                    type="file"
                    id="certs"
                    name="certs"
                    accept="image/*,application/pdf"
                    multiple />
                </label>
              </div>
            </div>
          </section>

          <!-- 8 -->
          <section class="card">
            <div class="card-head">
              <div class="num">08</div>
              <div>
                <h2>Contact Details</h2>
                <p>Trial call letters are sent on WhatsApp and email</p>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="field">
                  <label class="lbl" for="mobile"
                    >Mobile Number<em>*</em></label
                  >
                  <input
                    type="tel"
                    id="mobile"
                    name="mobile"
                    data-req
                    inputmode="numeric"
                    maxlength="10"
                    placeholder="10-digit number" />
                  <div class="msg">Enter a valid 10-digit mobile number.</div>
                </div>
                <div class="field">
                  <label class="lbl" for="whatsapp"
                    >WhatsApp Number<em>*</em></label
                  >
                  <input
                    type="tel"
                    id="whatsapp"
                    name="whatsapp"
                    data-req
                    inputmode="numeric"
                    maxlength="10"
                    placeholder="10-digit number" />
                  <label
                    class="hint"
                    style="
                      display: flex;
                      gap: 7px;
                      align-items: center;
                      margin-top: 7px;
                      cursor: pointer;
                    ">
                    <input
                      type="checkbox"
                      id="sameWa"
                      style="
                        width: 15px;
                        height: 15px;
                        accent-color: var(--orange);
                      " />
                    Same as mobile number
                  </label>
                  <div class="msg">Enter a valid 10-digit WhatsApp number.</div>
                </div>
              </div>
              <div class="field">
                <label class="lbl" for="email">Email ID<em>*</em></label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  data-req
                  placeholder="name@example.com"
                  autocomplete="email" />
                <div class="msg">Enter a valid email address.</div>
              </div>
              <div class="row three">
                <div class="field">
                  <label class="lbl" for="emgName"
                    >Emergency Contact Name<em>*</em></label
                  >
                  <input
                    type="text"
                    id="emgName"
                    name="emgName"
                    data-req
                    placeholder="Full name" />
                  <div class="msg">Enter the emergency contact name.</div>
                </div>
                <div class="field">
                  <label class="lbl" for="emgNo"
                    >Emergency Contact Number<em>*</em></label
                  >
                  <input
                    type="tel"
                    id="emgNo"
                    name="emgNo"
                    data-req
                    inputmode="numeric"
                    maxlength="10"
                    placeholder="10-digit number" />
                  <div class="msg">Enter a valid 10-digit number.</div>
                </div>
                <div class="field">
                  <label class="lbl" for="emgRel">Relationship<em>*</em></label>
                  <input
                    type="text"
                    id="emgRel"
                    name="emgRel"
                    data-req
                    placeholder="Father / Mother / Guardian" />
                  <div class="msg">Enter the relationship.</div>
                </div>
              </div>
            </div>
          </section>

          <!-- 9 -->
          <section class="card">
            <div class="card-head">
              <div class="num">09</div>
              <div>
                <h2>Declarations</h2>
                <p>All six confirmations are required to submit the form</p>
              </div>
            </div>
            <div class="card-body">
              <div class="field" id="declField">
                <label class="consent"
                  ><input type="checkbox" name="d1" data-decl />
                  <p>
                    <b>Trials participation consent</b>I voluntarily agree to
                    participate in the UPPHL Season 2 player trials and
                    understand that participation does not guarantee selection.
                  </p></label
                >
                <label class="consent"
                  ><input type="checkbox" name="d2" data-decl />
                  <p>
                    <b>Player declaration</b>All information, achievements,
                    certificates and documents submitted by me are true and
                    correct. False or unverifiable information may cancel my
                    registration.
                  </p></label
                >
                <label class="consent"
                  ><input type="checkbox" name="d3" data-decl />
                  <p>
                    <b>Medical &amp; fitness declaration</b>I am physically fit
                    for competitive handball trials and have disclosed any known
                    injury or medical condition relevant to my participation.
                  </p></label
                >
                <label class="consent"
                  ><input type="checkbox" name="d4" data-decl />
                  <p>
                    <b>Media &amp; promotional consent</b>UPPHL may use my name,
                    photographs, videos, trial footage and player profile for
                    league promotional, media, digital and marketing purposes.
                  </p></label
                >
                <label class="consent"
                  ><input type="checkbox" name="d5" data-decl />
                  <p>
                    <b>Rules &amp; regulations</b>I will follow all rules,
                    instructions, trial procedures and decisions communicated by
                    UPPHL and its authorised officials.
                  </p></label
                >
                <label class="consent"
                  ><input type="checkbox" name="d6" data-decl />
                  <p>
                    <b>Final acknowledgement</b>I have read and understood this
                    registration form and voluntarily submit my registration for
                    the UPPHL Season 2 player trials.
                  </p></label
                >
                <div class="msg">Tick all six declarations to continue.</div>
              </div>
              <div class="row three" style="margin-top: 16px">
                <div class="field">
                  <label class="lbl" for="signName"
                    >Signature (Type Full Name)<em>*</em></label
                  >
                  <input
                    type="text"
                    id="signName"
                    name="signName"
                    data-req
                    placeholder="Type your full name" />
                  <div class="msg">Type your name as signature.</div>
                </div>
                <div class="field">
                  <label class="lbl" for="signDate">Date<em>*</em></label>
                  <input type="date" id="signDate" name="signDate" data-req />
                  <div class="msg">Select the date.</div>
                </div>
                <div class="field">
                  <label class="lbl" for="signPlace">Place<em>*</em></label>
                  <input
                    type="text"
                    id="signPlace"
                    name="signPlace"
                    data-req
                    placeholder="e.g. Lucknow" />
                  <div class="msg">Enter the place.</div>
                </div>
              </div>
            </div>
          </section>

          <!-- 10 -->
          <section class="card" id="paySection">
            <div class="card-head">
              <div class="num">10</div>
              <div>
                <h2>Registration Fee &amp; Bank Payment</h2>
                <p>
                  Secure registration fee processing via ICICI Bank Gateway / UPI
                </p>
              </div>
            </div>
            <div class="card-body">
              <div class="fee">
                <div>
                  <span id="feeLabel">Season 2 Player Trial Registration Fee</span><span id="feeBase">₹1,000</span>
                </div>
                <div>
                  <span>Convenience &amp; Gateway Fee</span><span id="feeConv">₹0</span>
                </div>
                <div class="tot">
                  <span>Total Payable</span><span id="feeTotal">₹1,000</span>
                </div>
              </div>
              <div class="pay-status" id="payStatus">
                <span class="dot"></span><span id="payStatusTxt">Official UPPHL Season 2 Trials Registration Fee · Instant Bank Verification</span>
              </div>
              <div class="pay-methods">
                <span>UPI / QR</span><span>Debit / Credit Card</span><span>Net Banking</span><span>ICICI Eazypay Verified</span>
              </div>
            </div>
          </section>

          <div class="submit-wrap">
            <div class="form-err" id="formErr"></div>
            <button type="submit" class="btn" id="submitBtn">
              Submit registration
            </button>
            <p>
              By submitting, your details go to the UPPHL selection committee
              for verification. Keep your Aadhaar and certificates handy at the
              trial venue.
            </p>
          </div>
        </form>

        <!-- ============ ASIDE ============ -->
        <aside class="aside">
          <div class="pcard">
            <div class="pcard-top">
              <span class="eyebrow">UPPHL Season 2</span>
              <strong>Trial entry card</strong>
            </div>
            <div class="avatar" id="avatar"></div>
            <div class="pcard-body">
              <div class="pname" id="cName">Your name</div>
              <div class="ppos" id="cPos">Position not selected</div>
              <div class="stats">
                <div><b id="cHt">—</b><small>Height</small></div>
                <div><b id="cWt">—</b><small>Weight</small></div>
                <div><b id="cAge">—</b><small>Age</small></div>
              </div>
              <div class="stats" style="grid-template-columns: 1fr 1fr">
                <div><b id="cHand">—</b><small>Hand</small></div>
                <div><b id="cS1">—</b><small>Season 1</small></div>
              </div>
            </div>
          </div>
          <div class="note">
            <b>Before You Submit</b>
            <ul>
              <li>Name and DOB must match Aadhaar exactly.</li>
              <li>Photo should be recent and front-facing.</li>
              <li>Department players may need an NOC.</li>
              <li>Injury disclosure does not disqualify you.</li>
            </ul>
          </div>
        </aside>
      </div>

      <!-- ============ SUCCESS ============ -->
      <div class="done-screen" id="doneScreen">
        <div class="done-card">
          <div class="done-top">
            <div class="tick">✓</div>
            <h2>Registration Submitted</h2>
            <p style="margin: 8px 0 0; color: #d6def2; font-size: 14px">
              Save your registration ID — you will need it at the trial venue.
            </p>
          </div>
          <div class="done-body">
            <div class="regid" id="regId">—</div>

            <div class="creds">
              <b>Your UPPHL Season 2 Player Account</b>
              <div class="cred-row">
                <span>Login ID</span><strong id="credId">—</strong>
              </div>
              <div class="cred-row">
                <span>Password</span><strong id="credPw">—</strong>
              </div>
              <p id="mailNote">
                These details have also been emailed to
                <em id="credMail">your email</em>. Save them — you will need
                them to check your trial status and selection result.
              </p>
              <a class="btn" id="loginLink" href="#" onclick="openPlayerLoginModal(event); return false;"
                >Open player login</a
              >
            </div>

            <div class="sum" id="sumBox"></div>
            <div
              style="
                margin-top: 20px;
                display: flex;
                gap: 10px;
                justify-content: center;
                flex-wrap: wrap;
              ">
              <button class="btn ghost" onclick="window.print()">
                Print this page
              </button>
              <button class="btn ghost" onclick="location.reload()">
                Register another player
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ============ PAYMENT CHECKOUT ============ -->
    <div
      class="modal"
      id="payModal"
      role="dialog"
      aria-modal="true"
      aria-label="Payment checkout">
      <div class="sheet">
        <div class="sheet-head">
          <div>
            <b>UPPHL Season 2 trials</b><strong id="payAmt">₹500</strong>
          </div>
          <button
            class="x"
            type="button"
            id="payClose"
            aria-label="Close payment window">
            ✕
          </button>
        </div>

        <div id="payBody">
          <div class="tabs">
            <button class="tab on" type="button" data-pane="paneUpi">
              UPI
            </button>
            <button class="tab" type="button" data-pane="paneCard">Card</button>
            <button class="tab" type="button" data-pane="paneNet">
              Net banking
            </button>
          </div>

          <div class="pane on" id="paneUpi">
            <div class="field">
              <label class="lbl" for="upiId">UPI ID</label>
              <input type="text" id="upiId" placeholder="yourname@upi" />
              <div class="msg">
                Enter a valid UPI ID, for example name@okhdfcbank.
              </div>
            </div>
            <button class="btn" type="button" data-pay="UPI">
              Pay with UPI
            </button>
          </div>

          <div class="pane" id="paneCard">
            <div class="field">
              <label class="lbl" for="cardNo">Card Number</label>
              <input
                type="text"
                id="cardNo"
                inputmode="numeric"
                maxlength="19"
                placeholder="4111 1111 1111 1111" />
              <div class="msg">Enter a 16-digit card number.</div>
            </div>
            <div class="row">
              <div class="field">
                <label class="lbl" for="cardExp">Expiry</label>
                <input
                  type="text"
                  id="cardExp"
                  maxlength="5"
                  placeholder="MM/YY" />
                <div class="msg">Enter expiry as MM/YY.</div>
              </div>
              <div class="field">
                <label class="lbl" for="cardCvv">CVV</label>
                <input
                  type="text"
                  id="cardCvv"
                  inputmode="numeric"
                  maxlength="3"
                  placeholder="123" />
                <div class="msg">Enter the 3-digit CVV.</div>
              </div>
            </div>
            <button class="btn" type="button" data-pay="Card">
              Pay by card
            </button>
          </div>

          <div class="pane" id="paneNet">
            <div class="field">
              <label class="lbl" for="bank">Select Bank</label>
              <select id="bank">
                <option value="">Choose your bank</option>
                <option>State Bank of India</option>
                <option>HDFC Bank</option>
                <option>ICICI Bank</option>
                <option>Punjab National Bank</option>
                <option>Bank of Baroda</option>
                <option>Axis Bank</option>
                <option>Union Bank of India</option>
                <option>Kotak Mahindra Bank</option>
              </select>
              <div class="msg">Select a bank to continue.</div>
            </div>
            <button class="btn" type="button" data-pay="Net Banking">
              Pay by net banking
            </button>
          </div>

          <div class="secure">
            Payments are securely processed by ICICI Bank Eazypay Payment Gateway. UPPHL
            does not store your card, UPI or bank credentials.
          </div>
        </div>

        <div class="paying" id="paying">
          <div class="spin"></div>
          <p>Processing payment</p>
          <small>Do not close this window or press back</small>
        </div>
      </div>
    </div>

    <!-- Modern Footer -->

<script>
      /* ============================================================
   OPTIONAL BACKEND
   Paste a Google Apps Script web-app URL or Formspree endpoint
   below to receive submissions. Leave empty to run offline.
   ============================================================ */
      /* ============================================================
   1. BACKEND — paste your Google Apps Script web app URL here.
      (Code.gs file is supplied separately. Deploy it as
      "Anyone" access and paste the /exec URL below.)
      Without this URL nothing is saved and no email is sent.
   ============================================================ */
      const FORM_ENDPOINT = "";

      /* 2. Send uploaded documents to Drive along with the form.
      Set false if players are on slow connections and you will
      collect documents at the trial venue instead. */
      const UPLOAD_FILES = true;

      /* 3. Player login page URL (upphl-#) */
      const LOGIN_PAGE = "#";

      /* ============================================================
       4. PAYMENT — ICICI BANK EAZYPAY PAYMENT GATEWAY
       demo:true  -> simulated checkout, no money moves (offline testing)
       demo:false -> live ICICI Eazypay Payment Gateway via api/icici-initiate.php
       (ICICI Merchant ID & Secret Key are securely managed in api/config.php)
       ============================================================ */
      const PAYMENT = {
        enabled: true,
        demo: false, // Set to false for live ICICI Eazypay Gateway, true for local demo
        amount: 1000, // Season 2 trial registration fee
        convenience: 0,
        label: "Season 2 trial registration fee",
        brand: "ICICI Bank Orange PG",
      };
      const PAY_TOTAL = PAYMENT.amount + PAYMENT.convenience;
      const paymentState = {
        paid: false,
        id: "",
        method: "",
        amount: PAY_TOTAL,
      };

      const $ = (s) => document.querySelector(s);
      const form = $("#regForm");

      /* ---------- age from DOB ---------- */
      $("#dob").addEventListener("change", (e) => {
        const d = new Date(e.target.value);
        if (isNaN(d)) {
          $("#age").value = "";
          return;
        }
        const t = new Date();
        let a = t.getFullYear() - d.getFullYear();
        const m = t.getMonth() - d.getMonth();
        if (m < 0 || (m === 0 && t.getDate() < d.getDate())) a--;
        $("#age").value = a > 0 && a < 100 ? a + " years" : "";
        $("#cAge").textContent = a > 0 && a < 100 ? a : "—";
        updateProgress();
      });

      /* ---------- numeric-only inputs ---------- */
      ["mobile", "whatsapp", "emgNo"].forEach((id) => {
        $("#" + id).addEventListener(
          "input",
          (e) =>
            (e.target.value = e.target.value.replace(/\D/g, "").slice(0, 10)),
        );
      });
      $("#aadhaar").addEventListener("input", (e) => {
        const v = e.target.value.replace(/\D/g, "").slice(0, 12);
        e.target.value = v.replace(/(.{4})/g, "$1 ").trim();
      });
      $("#sameWa").addEventListener("change", (e) => {
        if (e.target.checked) {
          $("#whatsapp").value = $("#mobile").value;
          $("#whatsapp").readOnly = true;
        } else {
          $("#whatsapp").readOnly = false;
        }
        updateProgress();
      });
      $("#mobile").addEventListener("input", () => {
        if ($("#sameWa").checked) $("#whatsapp").value = $("#mobile").value;
      });

      /* ---------- conditional blocks ---------- */
      form.addEventListener("change", (e) => {
        if (e.target.dataset.cond !== undefined) {
          const group = form.querySelectorAll(
            'input[name="' + e.target.name + '"]',
          );
          group.forEach((r) => {
            if (r.dataset.cond) $("#" + r.dataset.cond).classList.remove("on");
          });
          if (e.target.dataset.cond)
            $("#" + e.target.dataset.cond).classList.add("on");
        }
      });

      /* ---------- consent visual ---------- */
      document.querySelectorAll(".consent input").forEach((c) => {
        c.addEventListener("change", () => {
          c.closest(".consent").classList.toggle("checked", c.checked);
          updateProgress();
        });
      });

      /* ---------- file uploads ---------- */
      function bindUpload(inputId, wrapId, isPhoto) {
        const inp = $("#" + inputId),
          wrap = $("#" + wrapId);
        inp.addEventListener("change", () => {
          const f = inp.files[0];
          const small = wrap.querySelector("small");
          if (!f) {
            wrap.classList.remove("done");
            return;
          }
          if (f.size > 5 * 1024 * 1024) {
            small.textContent =
              "File is larger than 5 MB — choose a smaller file";
            inp.value = "";
            wrap.classList.remove("done");
            return;
          }
          wrap.classList.add("done");
          small.textContent =
            inp.files.length > 1
              ? inp.files.length + " files selected"
              : f.name;
          if (isPhoto && f.type.startsWith("image/")) {
            const r = new FileReader();
            r.onload = (ev) => {
              $("#avatar").innerHTML =
                '<img src="' + ev.target.result + '" alt="Player photograph">';
              $("#photoIco").innerHTML =
                '<img src="' + ev.target.result + '" alt="">';
            };
            r.readAsDataURL(f);
          }
          wrap.closest(".field").classList.remove("bad");
          updateProgress();
        });
      }
      bindUpload("photo", "upPhoto", true);
      bindUpload("aadhaarFront", "upAF");
      bindUpload("aadhaarBack", "upAB");
      bindUpload("certs", "upCert");

      /* ---------- live entry card ---------- */
      $("#fullName").addEventListener(
        "input",
        (e) => ($("#cName").textContent = e.target.value.trim() || "Your name"),
      );
      $("#height").addEventListener(
        "input",
        (e) =>
          ($("#cHt").textContent = e.target.value
            ? e.target.value + " cm"
            : "—"),
      );
      $("#weight").addEventListener(
        "input",
        (e) =>
          ($("#cWt").textContent = e.target.value
            ? e.target.value + " kg"
            : "—"),
      );
      form.addEventListener("change", (e) => {
        if (e.target.name === "pos1") $("#cPos").textContent = e.target.value;
        if (e.target.name === "hand")
          $("#cHand").textContent = e.target.value.replace(" Hand", "");
        if (e.target.name === "season1")
          $("#cS1").textContent = e.target.value === "Yes" ? "Played" : "New";
      });

      /* ---------- progress ---------- */
      function requiredNodes() {
        const list = [];
        form
          .querySelectorAll("[data-req]")
          .forEach((el) => list.push(() => el.value.trim() !== ""));
        form.querySelectorAll("[data-req-group]").forEach((g) => {
          const n = g.querySelector("input").name;
          list.push(
            () => !!form.querySelector('input[name="' + n + '"]:checked'),
          );
        });
        form
          .querySelectorAll("[data-req-file]")
          .forEach((el) => list.push(() => el.files.length > 0));
        form
          .querySelectorAll("[data-decl]")
          .forEach((el) => list.push(() => el.checked));
        return list;
      }
      function updateProgress() {
        const checks = requiredNodes();
        const done = checks.filter((fn) => fn()).length;
        const pct = Math.round((done / checks.length) * 100);
        $("#pbFill").style.width = pct + "%";
        $("#pbPct").textContent = pct + "%";
      }
      form.addEventListener("input", updateProgress);
      form.addEventListener("change", updateProgress);
      updateProgress();

      /* ---------- validation ---------- */
      function fail(el, on) {
        const f = el.closest(".field");
        if (f) f.classList.toggle("bad", on);
        return f;
      }
      function validate() {
        let firstBad = null;
        const mark = (el, bad) => {
          const f = fail(el, bad);
          if (bad && !firstBad) firstBad = f;
        };

        form
          .querySelectorAll("[data-req]")
          .forEach((el) => mark(el, el.value.trim() === ""));
        form.querySelectorAll("[data-req-group]").forEach((g) => {
          const n = g.querySelector("input").name;
          mark(g, !form.querySelector('input[name="' + n + '"]:checked'));
        });
        form
          .querySelectorAll("[data-req-file]")
          .forEach((el) => mark(el, el.files.length === 0));

        const num = (id) => $("#" + id).value.replace(/\D/g, "");
        if (num("aadhaar").length !== 12) mark($("#aadhaar"), true);
        ["mobile", "whatsapp", "emgNo"].forEach((id) => {
          if (num(id).length !== 10) mark($("#" + id), true);
        });
        if (!/^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test($("#email").value.trim()))
          mark($("#email"), true);

        const decls = [...form.querySelectorAll("[data-decl]")];
        const declBad = decls.some((d) => !d.checked);
        $("#declField").classList.toggle("bad", declBad);
        if (declBad && !firstBad) firstBad = $("#declField");

        return firstBad;
      }

      /* ---------- submit ---------- */
      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const bad = validate();
        const box = $("#formErr");
        if (bad) {
          box.textContent =
            "Some required fields are missing or incorrect. The fields marked in red need your attention.";
          box.classList.add("on");
          bad.scrollIntoView({ behavior: "smooth", block: "center" });
          return;
        }
        box.classList.remove("on");

        if (PAYMENT.enabled && !paymentState.paid) {
          if (!PAYMENT.demo) {
            // Initiate Live ICICI Bank Orange PG Payment
            await initiateICICIPayment();
            return;
          }
          openPay();
          return;
        }
        await finalize();
      });

      /* ---------- credentials ---------- */
      function makeLoginId() {
        let seq = parseInt(localStorage.getItem("upphl_player_seq") || "0") + 1;
        localStorage.setItem("upphl_player_seq", seq.toString());
        return "UPPHL-S2-" + String(seq).padStart(3, "0");
      }
      function makePassword() {
        const set = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        let x = "";
        for (let i = 0; i < 8; i++)
          x += set[Math.floor(Math.random() * set.length)];
        return x;
      }

      /* ---------- Fast Mobile Image Compression Helper ---------- */
      async function compressImageFile(file, maxWidth = 1200, quality = 0.75) {
        if (!file || !file.type || !file.type.startsWith('image/')) return file;
        if (file.size < 250 * 1024) return file; // Skip if already under 250KB

        return new Promise((resolve) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
              let width = img.width;
              let height = img.height;
              if (width > maxWidth) {
                height = Math.round((height * maxWidth) / width);
                width = maxWidth;
              }
              const canvas = document.createElement('canvas');
              canvas.width = width;
              canvas.height = height;
              const ctx = canvas.getContext('2d');
              ctx.drawImage(img, 0, 0, width, height);
              canvas.toBlob(
                (blob) => {
                  if (blob && blob.size < file.size) {
                    resolve(new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), { type: 'image/jpeg' }));
                  } else {
                    resolve(file);
                  }
                },
                'image/jpeg',
                quality
              );
            };
            img.onerror = () => resolve(file);
            img.src = e.target.result;
          };
          reader.onerror = () => resolve(file);
          reader.readAsDataURL(file);
        });
      }

      /* ---------- ICICI Orange PG Payment Initiation ---------- */
      async function initiateICICIPayment() {
        const btn = $("#submitBtn");
        const box = $("#formErr");
        btn.disabled = true;
        btn.textContent = "Preparing files for payment…";

        try {
          const loginId = makeLoginId();
          const password = makePassword();

          const formData = new FormData(form);
          formData.append("playerId", loginId);
          formData.append("password", password);
          formData.append("amount", PAY_TOTAL);

          // Save player credentials & profile data to localStorage before leaving for payment gateway
          const playerObj = {};
          formData.forEach((v, k) => {
            if (!(v instanceof File)) playerObj[k] = v;
          });
          playerObj.playerId = loginId;
          playerObj.password = password;
          playerObj.registrationId = loginId;
          playerObj.amountPaid = "₹" + PAY_TOTAL;
          playerObj.submittedAt = new Date().toLocaleString("en-IN");
          localStorage.setItem("upphl_registered_player", JSON.stringify(playerObj));
          localStorage.setItem("upphl_player_profile", JSON.stringify(playerObj));

          // Fast mobile camera photo compression to avoid network timeouts on mobile/desktop
          btn.textContent = "Optimizing images for upload…";
          const photoInput = $("#photo");
          if (photoInput && photoInput.files && photoInput.files[0]) {
            const cPhoto = await compressImageFile(photoInput.files[0]);
            formData.set("photo", cPhoto, "photo.jpg");
          }

          const afInput = $("#aadhaarFront");
          if (afInput && afInput.files && afInput.files[0]) {
            const cAf = await compressImageFile(afInput.files[0]);
            formData.set("aadhaarFront", cAf, "aadhaar_front.jpg");
          }

          const abInput = $("#aadhaarBack");
          if (abInput && abInput.files && abInput.files[0]) {
            const cAb = await compressImageFile(abInput.files[0]);
            formData.set("aadhaarBack", cAb, "aadhaar_back.jpg");
          }

          const certsInput = $("#certs");
          if (certsInput && certsInput.files && certsInput.files.length > 0) {
            formData.delete("certs");
            formData.delete("certs[]");
            for (let i = 0; i < certsInput.files.length; i++) {
              const file = certsInput.files[i];
              if (file.type && file.type.startsWith("image/")) {
                const cCert = await compressImageFile(file, 1200, 0.75);
                formData.append("certs[]", cCert, file.name);
              } else {
                formData.append("certs[]", file, file.name);
              }
            }
          }

          btn.textContent = "Connecting to ICICI Bank Payment Gateway…";

          const response = await fetch("api/icici-initiate.php", {
            method: "POST",
            body: formData,
          });

          if (!response.ok) {
            const errText = await response.text();
            throw new Error(`Server returned status ${response.status}: ${errText.substring(0, 100)}`);
          }

          const result = await response.json();

          if (result.success && result.paymentUrl) {
            // Update stored playerId if server returned a refined ID
            if (result.playerId) {
              playerObj.playerId = result.playerId;
              localStorage.setItem("upphl_registered_player", JSON.stringify(playerObj));
              localStorage.setItem("upphl_player_profile", JSON.stringify(playerObj));
            }
            window.location.href = result.paymentUrl;
          } else {
            throw new Error(result.message || "Failed to generate ICICI payment session.");
          }
        } catch (err) {
          btn.disabled = false;
          btn.textContent = "Pay " + money(PAY_TOTAL) + " & submit registration";
          let msg = err.message || "Unable to reach server.";
          if (msg.includes("Failed to fetch") || msg.includes("NetworkError")) {
            msg = "Network/Upload Connection Error. Please check your internet connection and try again.";
          }
          box.textContent = "Payment Error: " + msg;
          box.classList.add("on");
        }
      }


      async function finalize() {
        const box = $("#formErr");
        const btn = $("#submitBtn");
        btn.disabled = true;
        btn.textContent = "Submitting registration…";

        const data = {};
        new FormData(form).forEach((v, k) => {
          if (!(v instanceof File)) data[k] = v;
        });
        data.age = $("#age").value;
        data.loginId = makeLoginId();
        data.password = makePassword();
        data.registrationId = data.loginId;
        data.submittedAt = new Date().toLocaleString("en-IN");
        if (PAYMENT.enabled) {
          data.paymentId = paymentState.id;
          data.paymentMethod = paymentState.method;
          data.amountPaid = "₹" + paymentState.amount;
          data.paymentMode = PAYMENT.demo ? "Demo" : "Live";
        }

        // Send data to PHP Backend asynchronously
        try {
          const formData = new FormData(form);
          formData.append('playerId', data.loginId);
          formData.append('password', data.password);

          fetch("api/submit-registration.php", {
            method: "POST",
            body: formData,
          }).catch(e => console.log("Submit error:", e));
        } catch (err) {}

        // Store registered player profile data in localStorage
        const photoFile = $("#photo").files && $("#photo").files.length > 0 ? $("#photo").files[0] : null;
        let photoB64 = "assets/images/default-player.png";
        if (photoFile) {
          try {
            photoB64 = await new Promise((res) => {
              const r = new FileReader();
              r.onload = (ev) => res(ev.target.result || "assets/images/default-player.png");
              r.onerror = () => res("assets/images/default-player.png");
              r.readAsDataURL(photoFile);
              setTimeout(() => res("assets/images/default-player.png"), 1000);
            });
          } catch(e) {}
        }

        const registeredPlayer = {
          playerId: data.loginId,
          fullName: data.fullName || "Player Name",
          dob: data.dob || "2000-01-01",
          age: data.age || "26 Years",
          gender: data.gender || "Male",
          height: data.ht ? data.ht + " cm" : "180 cm",
          weight: data.wt ? data.wt + " kg" : "78 kg",
          hand: data.hand || "Right Hand",
          primaryPos: data.pos1 || "Centre Back / Playmaker",
          secondaryPos: data.pos2 || "Right Back",
          experience: data.totalExp ? data.totalExp + " Years" : "5 Years",
          highestLevel: data.level || "State Level",
          club: data.club || "UP Handball Academy",
          tournaments: data.certsNote || "State Handball Championship - 2024",
          achievements: data.achievements || "State Level Winner - 2024",
          fitness: data.fitness || "Fully Fit",
          injuryHistory: data.injHistory || "No major injury till date",
          injuryDetails: data.injDetails || "No reported injury",
          address:
            (data.addr1 || "") +
            (data.city ? ", " + data.city : "") +
            (data.state ? ", " + data.state : ""),
          district: data.district || "Varanasi",
          state: data.state || "Uttar Pradesh",
          mobile: data.mobile || "",
          whatsapp: data.whatsapp || data.mobile || "",
          email: data.email || "",
          emgContact:
            (data.emgRel ? data.emgRel + " - " : "") + (data.emgNo || ""),
          photoUrl: photoB64,
          submittedAt: data.submittedAt,
        };

        localStorage.setItem(
          "upphl_registered_player",
          JSON.stringify(registeredPlayer),
        );
        localStorage.setItem(
          "upphl_player_profile",
          JSON.stringify(registeredPlayer),
        );

        // Add to all registered players directory list
        try {
          let allPlayers = JSON.parse(
            localStorage.getItem("upphl_all_players") || "[]",
          );
          allPlayers = allPlayers.filter(
            (p) => p.playerId !== registeredPlayer.playerId,
          );
          allPlayers.unshift(registeredPlayer);
          localStorage.setItem("upphl_all_players", JSON.stringify(allPlayers));
        } catch (e) {}

        if (window.updateNavProfileState) {
          window.updateNavProfileState();
        }

        $("#regId").textContent = data.loginId;
        $("#credId").textContent = data.loginId;
        $("#credPw").textContent = data.password;
        $("#credMail").textContent = data.email;
        $("#loginLink").href = "my-profile.php?id=" + encodeURIComponent(data.loginId);

        const rows = [
          ["Player name", data.fullName],
          ["Player ID", data.loginId],
          ["Age", data.age || "—"],
          ["Primary position", data.pos1 || "—"],
          ["Season 1 player", data.season1 || "—"],
          ["Highest level", data.level || "—"],
          ["District", data.district + ", " + data.state],
          ["Mobile", data.mobile],
          ["Submitted on", data.submittedAt],
        ];
        if (PAYMENT.enabled) {
          rows.push([
            "Fee paid",
            data.amountPaid + " (" + data.paymentMethod + ")",
          ]);
          rows.push(["Payment ID", data.paymentId]);
        }
        $("#sumBox").innerHTML = rows
          .map(
            (r) =>
              "<div><span>" +
              r[0] +
              "</span><span>" +
              (r[1] || "—") +
              "</span></div>",
          )
          .join("");
        $("#mainGrid").style.display = "none";
        document.querySelector(".progress-bar").style.display = "none";
        $("#doneScreen").classList.add("on");
        window.scrollTo({ top: 0, behavior: "smooth" });
      }

      /* ============================================================
   PAYMENT GATEWAY
   ============================================================ */
      const money = (n) => "₹" + n.toLocaleString("en-IN");
      if (PAYMENT.enabled) {
        $("#feeLabel").textContent = PAYMENT.label;
        $("#feeBase").textContent = money(PAYMENT.amount);
        $("#feeConv").textContent = money(PAYMENT.convenience);
        $("#feeTotal").textContent = money(PAY_TOTAL);
        $("#payAmt").textContent = money(PAY_TOTAL);
        $("#submitBtn").textContent =
          "Pay " + money(PAY_TOTAL) + " & submit registration";
        if (!PAYMENT.demo) $("#demoBanner").style.display = "none";
      } else {
        $("#paySection").style.display = "none";
      }

      const payModal = $("#payModal");
      function openPay() {
        payModal.classList.add("on");
        document.body.style.overflow = "hidden";
      }
      function closePay() {
        payModal.classList.remove("on");
        document.body.style.overflow = "";
      }
      $("#payClose").addEventListener("click", closePay);
      payModal.addEventListener("click", (e) => {
        if (e.target === payModal) closePay();
      });
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && payModal.classList.contains("on")) closePay();
      });

      document.querySelectorAll(".tab").forEach((t) =>
        t.addEventListener("click", () => {
          document
            .querySelectorAll(".tab")
            .forEach((x) => x.classList.remove("on"));
          document
            .querySelectorAll(".pane")
            .forEach((x) => x.classList.remove("on"));
          t.classList.add("on");
          $("#" + t.dataset.pane).classList.add("on");
        }),
      );

      $("#cardNo").addEventListener("input", (e) => {
        e.target.value = e.target.value
          .replace(/\D/g, "")
          .slice(0, 16)
          .replace(/(.{4})/g, "$1 ")
          .trim();
      });
      $("#cardCvv").addEventListener(
        "input",
        (e) => (e.target.value = e.target.value.replace(/\D/g, "").slice(0, 3)),
      );
      $("#cardExp").addEventListener("input", (e) => {
        let v = e.target.value.replace(/\D/g, "").slice(0, 4);
        e.target.value = v.length > 2 ? v.slice(0, 2) + "/" + v.slice(2) : v;
      });

      function payFieldBad(el, bad) {
        el.closest(".field").classList.toggle("bad", bad);
        return bad;
      }

      document.querySelectorAll("[data-pay]").forEach((btn) =>
        btn.addEventListener("click", () => {
          const method = btn.dataset.pay;
          let bad = false;
          if (method === "UPI") {
            bad = payFieldBad(
              $("#upiId"),
              !/^[\w.\-]{2,}@[a-z]{2,}$/i.test($("#upiId").value.trim()),
            );
          } else if (method === "Card") {
            bad =
              payFieldBad(
                $("#cardNo"),
                $("#cardNo").value.replace(/\D/g, "").length !== 16,
              ) | 0;
            bad =
              payFieldBad(
                $("#cardExp"),
                !/^(0[1-9]|1[0-2])\/\d{2}$/.test($("#cardExp").value),
              ) || bad;
            bad =
              payFieldBad($("#cardCvv"), $("#cardCvv").value.length !== 3) ||
              bad;
          } else {
            bad = payFieldBad($("#bank"), $("#bank").value === "");
          }
          if (bad) return;
          startGateway(method);
        }),
      );

      function startGateway(method) {
        $("#payBody").style.display = "none";
        $("#paying").classList.add("on");
        setTimeout(
          () =>
            paymentDone(
              method,
              "demo_" + Math.random().toString(36).slice(2, 12).toUpperCase(),
            ),
          1800,
        );
      }

      function paymentDone(method, txnId) {
        paymentState.paid = true;
        paymentState.id = txnId;
        paymentState.method = method;
        $("#paying").classList.remove("on");
        $("#payBody").style.display = "";
        closePay();
        const s = $("#payStatus");
        s.classList.add("paid");
        $("#payStatusTxt").innerHTML =
          "Payment received — " +
          money(PAY_TOTAL) +
          " by " +
          method +
          " · Payment ID " +
          txnId;
        $("#submitBtn").textContent = "Submit registration";
        finalize();
      }

      /* default signature date = today */
      $("#signDate").value = new Date().toISOString().slice(0, 10);

      /* ============================================================
         ICICI EAZYPAY PAYMENT GATEWAY RETURN HANDLER
         ============================================================ */
      (function checkPaymentReturn() {
        const urlParams = new URLSearchParams(window.location.search);
        const paymentStatus = urlParams.get("payment");

        if (paymentStatus === "success") {
          const pId = urlParams.get("playerId") || "UPPHL-S2-Player";
          const pPw = urlParams.get("pw") || "";
          const pName = urlParams.get("name") || "";
          const pEmail = urlParams.get("email") || "";
          const txn = urlParams.get("txn") || ("ICICI_" + Date.now());
          const amt = urlParams.get("amount") || PAYMENT.amount;

          let localProfile = {};
          try {
            localProfile = JSON.parse(localStorage.getItem("upphl_registered_player") || "{}");
          } catch(e) {}

          const finalPw = pPw || localProfile.password || "Sent to Mobile";
          const finalName = pName || localProfile.fullName || "Registered Player";
          const finalEmail = pEmail || localProfile.email || "Registered Email";

          $("#regId").textContent = pId;
          $("#credId").textContent = pId;
          $("#credPw").textContent = finalPw;
          $("#credMail").textContent = finalEmail;
          $("#loginLink").href = "my-profile.php?id=" + encodeURIComponent(pId);

          const rows = [
            ["Player ID", pId],
            ["Player Name", finalName],
            ["Registration Fee", "₹" + amt + " (Paid via ICICI Eazypay)"],
            ["ICICI Transaction ID", txn],
            ["Registration Status", "Verified & Submitted for Trials"]
          ];

          $("#sumBox").innerHTML = rows
            .map((r) => "<div><span>" + r[0] + "</span><span>" + (r[1] || "—") + "</span></div>")
            .join("");

          $("#mainGrid").style.display = "none";
          const pb = document.querySelector(".progress-bar");
          if (pb) pb.style.display = "none";
          $("#doneScreen").classList.add("on");
          window.scrollTo({ top: 0, behavior: "smooth" });
        } else if (paymentStatus === "failed") {
          const err = urlParams.get("err") || "DECLINED";
          const box = $("#formErr");
          box.textContent = "ICICI Bank Payment could not be completed (Status: " + err + "). Please retry registration.";
          box.classList.add("on");
        }
      })();
    </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
