/* =========================================================
   TIXORA ADMIN
   ADMIN.JS
========================================================= */


/* =========================================================
   SECTION DATA
========================================================= */

const sectionTitles = {

    dashboard:
        "Dashboard",

    events:
        "Event Management",

    categories:
        "Categories",

    tickets:
        "Ticket Management",

    seats:
        "Seat Management",

    orders:
        "Orders",

    payments:
        "Payments",

    "ticket-validation":
        "QR Validation",

    users:
        "User Management",

    articles:
        "Articles",

    reports:
        "Reports"

};


/* =========================================================
   SHOW SECTION
========================================================= */

function showSection(sectionId) {

    const sections =
        document.querySelectorAll(
            ".admin-section"
        );

    sections.forEach(section => {
        section.classList.remove(
            "active"
        );
    });

    const target =
        document.getElementById(
            sectionId
        );

    if (target) {
        target.classList.add(
            "active"
        );
    }

    const menuItems =
        document.querySelectorAll(
            ".menu-item"
        );

    menuItems.forEach(item => {

        item.classList.remove(
            "active"
        );

        if (
            item.dataset.section ===
            sectionId
        ) {
            item.classList.add(
                "active"
            );
        }
    });

    const pageTitle =
        document.getElementById(
            "pageTitle"
        );

    if (
        pageTitle &&
        sectionTitles[sectionId]
    ) {
        pageTitle.textContent =
            sectionTitles[sectionId];
    }

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });

    closeSidebar();
}

window.showSection = showSection;


/* =========================================================
   SIDEBAR
========================================================= */

function toggleSidebar() {

    const sidebar =
        document.getElementById(
            "sidebar"
        );


    sidebar.classList.toggle(
        "open"
    );
}


function closeSidebar() {

    const sidebar =
        document.getElementById(
            "sidebar"
        );


    if (
        window.innerWidth <= 800
    ) {

        sidebar.classList.remove(
            "open"
        );

    }

}



/* =========================================================
   EVENT MODAL
========================================================= */

function openEditEventForm(
    id,
    name,
    category,
    location,
    venue,
    eventDate,
    status,
    description
) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_location').value = location;
    document.getElementById('edit_venue').value = venue;
    document.getElementById('edit_event_date').value = eventDate;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_description').value = description ?? '';

    document.getElementById('editEventForm').action =
        `/admin/events/${id}`;

    document.getElementById('editEventModal').classList.add('active');
}

/* =========================================================
   REJECT EVENT MODAL
========================================================= */

function openRejectEventForm(eventId, eventName) {

    const modal =
        document.getElementById(
            'rejectEventModal'
        );

    const form =
        document.getElementById(
            'rejectEventForm'
        );

    const eventNameText =
        document.getElementById(
            'rejectEventName'
        );

    if (!modal || !form) {

        console.error(
            'Modal reject event tidak ditemukan.'
        );

        return;
    }

    form.action =
        `/admin/events/${eventId}/reject`;

    if (eventNameText) {

        eventNameText.textContent =
            `Berikan alasan penolakan untuk event "${eventName}".`;

    }

    const textarea =
        form.querySelector(
            'textarea[name="rejection_reason"]'
        );

    if (textarea) {

        textarea.value = '';

    }

    modal.classList.add('active');

    document.body.style.overflow = 'hidden';
}

/* =========================================================
   ADD EVENT MODAL
========================================================= */

function openEventForm() {
    const modal = document.getElementById('eventFormModal');

    if (!modal) {
        console.error('Modal eventFormModal tidak ditemukan.');
        return;
    }

    modal.classList.add('active');
}


/* =========================================================
   CLOSE MODAL
========================================================= */
function closeAdminModal(id) {

    const modal =
        document.getElementById(
            id
        );


    if (!modal) return;


    modal.classList.remove(
        "active"
    );


    document.body.style.overflow =
        "";
}


/* =========================================================
   SAVE EVENT
========================================================= */

function saveEvent(event) {

    event.preventDefault();

    const form = event.target;

    const namaEvent = form.querySelector('input[type="text"]').value;
    const kategori = form.querySelector('select').value;

    const inputs = form.querySelectorAll('input[type="text"]');

    const lokasi = inputs[1].value;

    const tanggal = form.querySelector('input[type="date"]').value;

    const statusSelects = form.querySelectorAll('select');
    const status = statusSelects[1].value;

    const tableBody = document.querySelector('#eventTable tbody');

    if (!tableBody) {
        return;
    }

    const row = document.createElement('tr');

    row.innerHTML = `
        <td>
            <strong>${namaEvent}</strong>
        </td>

        <td>
            ${kategori}
        </td>

        <td>
            ${lokasi}
        </td>

        <td>
            ${tanggal}
        </td>

        <td>
            -
        </td>

        <td>
            <span class="status-pill orange">
                ${status.toUpperCase()}
            </span>
        </td>

        <td>
            <div class="action-buttons">

                <button type="button">
                    <i class="fa-solid fa-pen"></i>
                </button>

                <button type="button">
                    <i class="fa-solid fa-trash"></i>
                </button>

            </div>
        </td>
    `;

    tableBody.appendChild(row);

    closeAdminModal('eventFormModal');

    form.reset();
}


/* =========================================================
   EDIT
========================================================= */

function editItem(
    itemName
) {

    showToast(
        `Edit "${itemName}" akan terhubung ke database pada tahap backend.`
    );
}


/* =========================================================
   DELETE
========================================================= */

function deleteItem(
    itemName
) {

    const confirmation =
        confirm(
            `Yakin ingin menghapus "${itemName}"?`
        );


    if (!confirmation) {

        return;

    }


    showToast(
        `"${itemName}" berhasil dihapus. (Prototype)`
    );
}


/* =========================================================
   EVENT SEARCH & FILTER
========================================================= */

function filterAdminEvents() {

    const searchInput =
        document.getElementById('eventSearch');

    const categoryFilter =
        document.getElementById('eventCategoryFilter');

    const statusFilter =
        document.getElementById('eventStatusFilter');

    const approvalFilter =
        document.getElementById('eventApprovalFilter');

    const rows =
        document.querySelectorAll(
            '#eventTable tbody tr[data-event-row]'
        );

    const keyword =
        searchInput
            ? searchInput.value.toLowerCase().trim()
            : '';

    const category =
        categoryFilter
            ? categoryFilter.value.toLowerCase()
            : '';

    const status =
        statusFilter
            ? statusFilter.value
            : '';

    const approval =
        approvalFilter
            ? approvalFilter.value
            : '';

    rows.forEach(row => {

        const eventName =
            row.dataset.name?.toLowerCase() || '';

        const eventCategory =
            row.dataset.category?.toLowerCase() || '';

        const eventLocation =
            row.dataset.location?.toLowerCase() || '';

        const eventVenue =
            row.dataset.venue?.toLowerCase() || '';

        const eventStatus =
            row.dataset.status || '';

        const eventApproval =
            row.dataset.approval || '';

        const matchesSearch =
            eventName.includes(keyword) ||
            eventCategory.includes(keyword) ||
            eventLocation.includes(keyword) ||
            eventVenue.includes(keyword);

        const matchesCategory =
            !category ||
            eventCategory === category;

        const matchesStatus =
            !status ||
            eventStatus === status;

        const matchesApproval =
            !approval ||
            eventApproval === approval;

        row.style.display =
            matchesSearch &&
            matchesCategory &&
            matchesStatus &&
            matchesApproval
                ? ''
                : 'none';
    });
}


/* =========================================================
   ADD CATEGORY
========================================================= */

function addCategory() {

    showToast(
        "Form kategori akan tersedia pada tahap database."
    );
}

function openCategoryForm() {
    const modal = document.getElementById('categoryFormModal');

    if (!modal) {
        console.error('Modal categoryFormModal tidak ditemukan.');
        return;
    }

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}


/* =========================================================
   ADD ARTICLE
========================================================= */

function addArticle() {

    showToast(
        "Form artikel akan tersedia pada tahap database."
    );
}


/* =========================================================
   QR SIMULATION
========================================================= */

function simulateScan() {

    const result =
        document.getElementById(
            "validationResult"
        );


    result.innerHTML = `

        <div
            style="
                border:1px solid #dcefe0;
                background:#f2fbf4;
                border-radius:12px;
                padding:22px;
            "
        >

            <div
                style="
                    width:54px;
                    height:54px;
                    background:#dff5e4;
                    color:#34a04e;
                    border-radius:50%;
                    display:grid;
                    place-items:center;
                    margin:0 auto 12px;
                    font-size:22px;
                "
            >

                <i class="fa-solid fa-check"></i>

            </div>


            <div
                style="
                    text-align:center;
                "
            >

                <strong
                    style="
                        display:block;
                        color:#299746;
                        font-size:15px;
                    "
                >
                    VALID TICKET
                </strong>


                <span
                    style="
                        display:block;
                        color:#888;
                        font-size:8px;
                        margin-top:3px;
                    "
                >
                    QR berhasil diverifikasi
                </span>

            </div>


            <div
                style="
                    margin-top:18px;
                    padding-top:15px;
                    border-top:1px solid #dcefe0;
                    font-size:9px;
                    line-height:2;
                "
            >

                <strong>
                    Ticket ID:
                </strong>

                TIX-2026-883421

                <br>

                <strong>
                    Customer:
                </strong>

                Haikal

                <br>

                <strong>
                    Event:
                </strong>

                Jakarta Music Fest

                <br>

                <strong>
                    Ticket:
                </strong>

                VIP 1

                <br>

                <strong>
                    Seat:
                </strong>

                A01

                <br>

                <strong>
                    Status:
                </strong>

                PAID

            </div>


            <button
                onclick="checkInTicket()"
                style="
                    width:100%;
                    height:42px;
                    border:none;
                    border-radius:8px;
                    background:#299746;
                    color:white;
                    margin-top:15px;
                    font-size:9px;
                    font-weight:900;
                "
            >

                CHECK-IN

            </button>

        </div>

    `;
}


/* =========================================================
   CHECK IN
========================================================= */

function checkInTicket() {

    showToast(
        "Ticket berhasil CHECK-IN."
    );


    const result =
        document.getElementById(
            "validationResult"
        );


    setTimeout(
        () => {

            result.innerHTML = `

                <div
                    style="
                        border:1px solid #dddde3;
                        background:#f8f8fa;
                        border-radius:12px;
                        padding:25px;
                        text-align:center;
                    "
                >

                    <div
                        style="
                            width:54px;
                            height:54px;
                            background:#eeeef1;
                            color:#68686f;
                            border-radius:50%;
                            display:grid;
                            place-items:center;
                            margin:auto;
                        "
                    >

                        <i class="fa-solid fa-check"></i>

                    </div>


                    <h3
                        style="
                            margin-top:11px;
                            font-size:15px;
                        "
                    >
                        CHECK-IN BERHASIL
                    </h3>


                    <p
                        style="
                            margin-top:5px;
                            font-size:8px;
                            color:#888;
                        "
                    >
                        Tiket sudah digunakan
                        untuk masuk venue.
                    </p>

                </div>

            `;

        },
        700
    );
}


/* =========================================================
   TOAST
========================================================= */

function showToast(
    message
) {

    const toast =
        document.getElementById(
            "adminToast"
        );


    const text =
        document.getElementById(
            "adminToastMessage"
        );


    if (!toast || !text) {

        return;

    }


    text.textContent =
        message;


    toast.classList.add(
        "show"
    );


    clearTimeout(
        window.adminToastTimer
    );


    window.adminToastTimer =
        setTimeout(
            () => {

                toast.classList.remove(
                    "show"
                );

            },
            2800
        );
}


/* =========================================================
   ADMIN LOGOUT
========================================================= */

function adminLogout() {

    const confirmation =
        confirm(
            "Apakah kamu yakin ingin logout?"
        );


    if (!confirmation) {

        return;

    }


    showToast(
        "Logout berhasil. (Prototype)"
    );


    setTimeout(
        () => {

            window.location.href =
                "index.html";

        },
        900
    );
}


/* =========================================================
   WINDOW RESIZE
========================================================= */

window.addEventListener(
    "resize",
    function() {

        if (
            window.innerWidth > 800
        ) {

            document
                .getElementById(
                    "sidebar"
                )
                ?.classList.remove(
                    "open"
                );

        }

    }
);

window.closeAdminModal = closeAdminModal;
window.saveEvent = saveEvent;
window.openEditEventForm = openEditEventForm;
window.openRejectEventForm = openRejectEventForm;
window.openEventForm = openEventForm;
window.openCategoryForm = openCategoryForm;
window.showSection = showSection;
window.toggleSidebar = toggleSidebar;
/* =========================================================
   INITIAL LOAD
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        showSection('dashboard');

       const searchInput =
    document.getElementById('eventSearch');

const categoryFilter =
    document.getElementById(
        'eventCategoryFilter'
    );

const statusFilter =
    document.getElementById(
        'eventStatusFilter'
    );

const approvalFilter =
    document.getElementById(
        'eventApprovalFilter'
    );

        /* SEARCH SAAT MENGETIK */
        searchInput?.addEventListener(
            'input',
            function () {
                filterAdminEvents();
            }
        );

        /* SEARCH SAAT TEKAN ENTER */
        searchInput?.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Enter') {

                    event.preventDefault();

                    filterAdminEvents();

                }

            }
        );

        /* FILTER KATEGORI */
        categoryFilter?.addEventListener(
            'change',
            function () {
                filterAdminEvents();
            }
        );

        /* FILTER STATUS */
        statusFilter?.addEventListener(
            'change',
            function () {
                filterAdminEvents();
            }
        );
        
        /* FILTER APPROVAL */
        approvalFilter?.addEventListener(
            'change',
            function () {
                filterAdminEvents();
            }
        );

    }
);