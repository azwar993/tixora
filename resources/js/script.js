/* =========================================================
   TIXORA EVENT TICKETING
   SCRIPT.JS
   ========================================================= */


/* =========================================================
   1. GLOBAL STATE
   ========================================================= */

let currentUser = null;

let currentEvent = null;

let selectedTicket = null;

let selectedSeat = null;

let selectedPaymentMethod = "QRIS";

let currentOrder = null;

let favoriteEvents = JSON.parse(
    localStorage.getItem("tixoraFavorites")
) || [];

let registeredUsers = JSON.parse(
    localStorage.getItem("tixoraUsers")
) || [];

let orders = JSON.parse(
    localStorage.getItem("tixoraOrders")
) || [];


/* =========================================================
   2. DEMO USER
   ========================================================= */

if (registeredUsers.length === 0) {

    registeredUsers.push({
        name: "Demo User",
        email: "demo@tixora.id",
        password: "123456"
    });

    localStorage.setItem(
        "tixoraUsers",
        JSON.stringify(registeredUsers)
    );
}


/* =========================================================
   3. EVENT DATABASE - PROTOTYPE
   ========================================================= */

const events = [

    /* =====================================================
       MPL
    ===================================================== */

    {
        id: "mpl-id-s18",

        title: "MPL ID Season 18",

        category: "Esports",

        subcategory: "Mobile Legends",

        location: "Jakarta",

        venue: "XO Hall / MPL Arena",

        address: "Jakarta Barat",

        date: "2026",

        time: "Sesuai jadwal pertandingan",

        status: "ongoing",

        image:
            "https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1000&q=90",

        description:
            "Saksikan pertandingan Mobile Legends Professional League Indonesia secara langsung dan rasakan atmosfer kompetisi esports terbesar.",

        lineup: [
            "MPL ID",
            "Professional Teams",
            "Mobile Legends"
        ],

        recommended: true,

        tickets: [

            {
                name: "REGULAR",
                description: "Regular Match Ticket",
                price: 85000
            },

            {
                name: "VIP",
                description: "VIP Match Ticket",
                price: 150000
            }

        ],

        seats: [
            "REG-A01",
            "REG-A02",
            "REG-A03",
            "REG-A04",
            "VIP-A01",
            "VIP-A02",
            "VIP-A03",
            "VIP-A04"
        ]
    },


    /* =====================================================
       KAHITNA
    ===================================================== */

    {
        id: "kahitna-40",

        title: "Kahitna 40 Tahun",

        category: "Music",

        subcategory: "Concert",

        location: "Tangerang",

        venue: "NICE PIK 2",

        address: "Tangerang",

        date: "5 September 2026",

        time: "19:00 WIB",

        status: "coming",

        image:
            "https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1000&q=90",

        description:
            "Perayaan perjalanan 40 tahun Kahitna dalam sebuah konser spesial dengan pengalaman musik dan entertainment.",

        lineup: [
            "Kahitna",
            "Special Guest",
            "40 Years Celebration"
        ],

        recommended: true,

        tickets: [

            {
                name: "FESTIVAL B",
                description: "Festival Area",
                price: 975000
            },

            {
                name: "FESTIVAL A",
                description: "Festival Area Premium",
                price: 1350000
            },

            {
                name: "PLATINUM PLUS",
                description: "Premium Seating",
                price: 2000000
            },

            {
                name: "SUPER DIAMOND",
                description: "Premium Experience",
                price: 5000000
            }

        ],

        seats: [
            "FB-A01",
            "FB-A02",
            "FB-A03",
            "FB-A04",
            "FA-A01",
            "FA-A02",
            "FA-A03",
            "PP-A01",
            "PP-A02",
            "SD-A01",
            "SD-A02"
        ]
    },


    /* =====================================================
       NADIN
    ===================================================== */

    {
        id: "nadin-sunset",

        title: "Sunset di Pantai",

        category: "Music",

        subcategory: "Concert",

        location: "PIK 2",

        venue: "Land's End PIK 2",

        address: "PIK 2, Tangerang",

        date: "19 September 2026",

        time: "16:00 WIB",

        status: "coming",

        image:
            "https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1000&q=90",

        description:
            "Event musik outdoor dengan suasana sunset di pantai dan penampilan dari musisi pilihan Indonesia.",

        lineup: [
            "Nadin Amizah",
            "Sal Priadi",
            "Perunggu",
            "Juicy Luicy"
        ],

        recommended: true,

        tickets: [

            {
                name: "REGULAR",
                description: "Harga mengikuti penjualan resmi",
                price: null
            },

            {
                name: "VIP",
                description: "Harga mengikuti penjualan resmi",
                price: null
            }

        ],

        seats: [
            "A01",
            "A02",
            "A03",
            "A04",
            "B01",
            "B02",
            "B03",
            "B04"
        ]
    },


    /* =====================================================
       JAKARTA MUSIC FEST
    ===================================================== */

    {
        id: "jakarta-music-fest",

        title: "Jakarta Music Fest",

        category: "Music",

        subcategory: "Festival",

        location: "Jakarta",

        venue: "GBK Jakarta",

        address: "Gelora Bung Karno",

        date: "12 September 2026",

        time: "18:00 WIB",

        status: "ongoing",

        image:
            "https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1000&q=90",

        description:
            "Festival musik dengan berbagai performer pilihan dan pengalaman entertainment untuk para penonton.",

        lineup: [
            "Local Artists",
            "Jakarta Music Fest",
            "Special Guest"
        ],

        recommended: true,

        tickets: [

            {
                name: "VIP 1",
                description: "Best View",
                price: 2500000
            },

            {
                name: "VIP 2",
                description: "Great View",
                price: 2000000
            },

            {
                name: "VVIP",
                description: "Premium Experience",
                price: 4000000
            },

            {
                name: "REGULAR",
                description: "Standard Area",
                price: 850000
            }

        ],

        seats: [
            "A01",
            "A02",
            "A03",
            "A04",
            "A05",
            "B01",
            "B02",
            "B03",
            "B04",
            "B05",
            "C01",
            "C02",
            "C03",
            "C04",
            "C05"
        ]
    },


    /* =====================================================
       SOUNWAVE
    ===================================================== */

    {
        id: "soundwave-jakarta",

        title: "Soundwave Jakarta",

        category: "Concert",

        subcategory: "Music",

        location: "Jakarta",

        venue: "Jakarta International Stadium",

        address: "Jakarta",

        date: "24 October 2026",

        time: "19:30 WIB",

        status: "coming",

        image:
            "https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?auto=format&fit=crop&w=1000&q=90",

        description:
            "Konser musik dengan pengalaman visual dan audio spektakuler untuk para penikmat musik.",

        lineup: [
            "National Artists",
            "Special Guest",
            "Soundwave"
        ],

        recommended: true,

        tickets: [

            {
                name: "REGULAR",
                description: "Standard Area",
                price: 1200000
            },

            {
                name: "VIP",
                description: "Premium Area",
                price: 2500000
            },

            {
                name: "VVIP",
                description: "Premium Experience",
                price: 4500000
            }

        ],

        seats: [
            "REG-A01",
            "REG-A02",
            "REG-A03",
            "VIP-B01",
            "VIP-B02",
            "VIP-B03",
            "VVIP-C01",
            "VVIP-C02"
        ]
    },


    /* =====================================================
       FOOTBALL
    ===================================================== */

    {
        id: "football-jabodetabek",

        title: "JABODETABEK Football Cup",

        category: "Sports",

        subcategory: "Football",

        location: "Bekasi",

        venue: "Stadion Patriot",

        address: "Bekasi",

        date: "20 September 2026",

        time: "19:00 WIB",

        status: "coming",

        image:
            "https://images.unsplash.com/photo-1579952363873-27f3bade9f55?auto=format&fit=crop&w=1000&q=90",

        description:
            "Pertandingan sepak bola dengan atmosfer stadion dan pengalaman menonton langsung bersama para supporter.",

        lineup: [
            "Football Cup",
            "JABODETABEK Teams",
            "Special Match"
        ],

        recommended: false,

        tickets: [

            {
                name: "REGULAR",
                description: "Regular Tribune",
                price: 350000
            },

            {
                name: "VIP",
                description: "VIP Tribune",
                price: 750000
            },

            {
                name: "VVIP",
                description: "Premium Tribune",
                price: 1200000
            }

        ],

        seats: [
            "REG-A01",
            "REG-A02",
            "REG-A03",
            "REG-A04",
            "VIP-B01",
            "VIP-B02",
            "VIP-B03",
            "VVIP-C01",
            "VVIP-C02"
        ]
    },


    /* =====================================================
       CREATIVE FESTIVAL
    ===================================================== */

    {
        id: "creative-festival",

        title: "Jakarta Creative Festival",

        category: "Festival",

        subcategory: "Entertainment",

        location: "Tangerang",

        venue: "ICE BSD",

        address: "Tangerang",

        date: "10 October 2026",

        time: "10:00 WIB",

        status: "coming",

        image:
            "https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1000&q=90",

        description:
            "Festival kreativitas yang mempertemukan komunitas, kreator, pelaku industri, dan masyarakat.",

        lineup: [
            "Creative Community",
            "Local Creator",
            "Entertainment"
        ],

        recommended: false,

        tickets: [

            {
                name: "REGULAR",
                description: "Festival Entry",
                price: 250000
            },

            {
                name: "VIP",
                description: "VIP Entry",
                price: 500000
            }

        ],

        seats: [
            "R-A01",
            "R-A02",
            "R-A03",
            "V-A01",
            "V-A02"
        ]
    },


    /* =====================================================
       HINDIA
    ===================================================== */

    {
        id: "hindia-jakarta",

        title: "Hindia - Jakarta Fair",

        category: "Music",

        subcategory: "Concert",

        location: "Jakarta",

        venue: "JIExpo Kemayoran",

        address: "Jakarta Pusat",

        date: "5 July 2026",

        time: "19:00 WIB",

        status: "past",

        image:
            "https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=1000&q=90",

        description:
            "Penampilan Hindia dalam rangkaian acara Jakarta Fair dengan konsep bundling tiket masuk dan konser.",

        lineup: [
            "Hindia"
        ],

        recommended: false,

        tickets: [

            {
                name: "REGULAR",
                description: "Bundling Entry + Concert",
                price: 80000
            },

            {
                name: "VIP",
                description: "Bundling Premium",
                price: 250000
            }

        ],

        seats: []
    },


    /* =====================================================
       FEAST
    ===================================================== */

    {
        id: "feast-jakarta",

        title: ".Feast - Jakarta Fair",

        category: "Music",

        subcategory: "Concert",

        location: "Jakarta",

        venue: "JIExpo Kemayoran",

        address: "Jakarta Pusat",

        date: "5 July 2026",

        time: "19:00 WIB",

        status: "past",

        image:
            "https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1000&q=90",

        description:
            "Penampilan .Feast dalam rangkaian Jakarta Fair dengan pilihan tiket bundling.",

        lineup: [
            ".Feast"
        ],

        recommended: false,

        tickets: [

            {
                name: "REGULAR",
                description: "Bundling Entry + Concert",
                price: 80000
            },

            {
                name: "VIP",
                description: "Bundling Premium",
                price: 250000
            }

        ],

        seats: []
    },


    /* =====================================================
       SAL PRIADI
    ===================================================== */

    {
        id: "sal-jakarta-fair",

        title: "Sal Priadi - Jakarta Fair",

        category: "Music",

        subcategory: "Concert",

        location: "Jakarta",

        venue: "JIExpo Kemayoran",

        address: "Jakarta Pusat",

        date: "7 July 2026",

        time: "19:00 WIB",

        status: "past",

        image:
            "https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1000&q=90",

        description:
            "Penampilan Sal Priadi dalam rangkaian Jakarta Fair.",

        lineup: [
            "Sal Priadi"
        ],

        recommended: false,

        tickets: [

            {
                name: "REGULAR",
                description: "Bundling Entry + Concert",
                price: 80000
            },

            {
                name: "VIP",
                description: "Bundling Premium",
                price: 250000
            }

        ],

        seats: []
    },


    /* =====================================================
       AFGAN
    ===================================================== */

    {
        id: "afgan-retrospektif",

        title: "Afgan - Retrospektif: The Concert",

        category: "Music",

        subcategory: "Concert",

        location: "Jakarta",

        venue: "Plenary Hall JCC",

        address: "Jakarta Pusat",

        date: "18 July 2026",

        time: "19:00 WIB",

        status: "past",

        image:
            "https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1000&q=90",

        description:
            "Konser retrospektif Afgan yang menghadirkan perjalanan musik dan karya-karya terbaiknya.",

        lineup: [
            "Afgan"
        ],

        recommended: false,

        tickets: [

            {
                name: "REGULAR",
                description: "Regular Seating",
                price: 600000
            },

            {
                name: "PREMIUM",
                description: "Premium Seating",
                price: 2500000
            },

            {
                name: "VVIP",
                description: "Premium Experience",
                price: 4750000
            }

        ],

        seats: []
    },


    /* =====================================================
       LYODRA
    ===================================================== */

    {
        id: "lyodra-intimate",

        title: "Lyodra - Intimate Show",

        category: "Music",

        subcategory: "Concert",

        location: "Jakarta Selatan",

        venue: "AM Lounge, ÉLYSÉE SCBD",

        address: "Jakarta Selatan",

        date: "27 November 2025",

        time: "20:00 WIB",

        status: "past",

        image:
            "https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1000&q=90",

        description:
            "Pertunjukan intimate Lyodra dengan suasana eksklusif dan konsep minimum charge meja untuk beberapa kategori.",

        lineup: [
            "Lyodra"
        ],

        recommended: false,

        tickets: [

            {
                name: "REGULAR",
                description: "Intimate Show",
                price: 250000
            },

            {
                name: "PREMIUM",
                description: "Premium Table",
                price: 6000000
            }

        ],

        seats: []
    },


    /* =====================================================
       SHEILA ON 7
    ===================================================== */

    {
        id: "sheila-on-7",

        title: "Sheila On 7 - Rendezvous 2025",

        category: "Music",

        subcategory: "Concert",

        location: "Jakarta",

        venue: "Istora Senayan",

        address: "Jakarta Pusat",

        date: "9 August 2025",

        time: "19:00 WIB",

        status: "past",

        image:
            "https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?auto=format&fit=crop&w=1000&q=90",

        description:
            "Rendezvous 2025 dari Sheila On 7 di Istora Senayan.",

        lineup: [
            "Sheila On 7"
        ],

        recommended: false,

        tickets: [],

        seats: []
    },


    /* =====================================================
       FOR REVENGE BEKASI
    ===================================================== */

    {
        id: "for-revenge-bekasi",

        title: "For Revenge - AEON Mall Deltamas",

        category: "Music",

        subcategory: "Concert",

        location: "Bekasi",

        venue: "AEON Mall Deltamas",

        address: "Bekasi",

        date: "31 May 2026",

        time: "19:00 WIB",

        status: "past",

        image:
            "https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?auto=format&fit=crop&w=1000&q=90",

        description:
            "Penampilan For Revenge di AEON Mall Deltamas Bekasi.",

        lineup: [
            "For Revenge"
        ],

        recommended: false,

        tickets: [],

        seats: []
    },


    /* =====================================================
       FOR REVENGE ICE BSD
    ===================================================== */

    {
        id: "for-revenge-ice",

        title: "For Revenge - ICE BSD",

        category: "Music",

        subcategory: "Concert",

        location: "Tangerang",

        venue: "Indonesia Convention Exhibition",

        address: "BSD / Tangerang",

        date: "13 June 2026",

        time: "19:00 WIB",

        status: "past",

        image:
            "https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1000&q=90",

        description:
            "Penampilan For Revenge di Indonesia Convention Exhibition BSD.",

        lineup: [
            "For Revenge"
        ],

        recommended: false,

        tickets: [],

        seats: []
    }

];


/* =========================================================
   4. FORMAT PRICE
   ========================================================= */

function formatRupiah(price) {

    if (
        price === null ||
        price === undefined ||
        isNaN(price)
    ) {
        return "Cek harga";
    }

    return new Intl.NumberFormat(
        "id-ID",
        {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0
        }
    ).format(price);
}


/* =========================================================
   5. GET CHEAPEST PRICE
   ========================================================= */

function getCheapestPrice(event) {

    const prices = event.tickets
        .map(ticket => ticket.price)
        .filter(price =>
            price !== null &&
            typeof price === "number"
        );

    if (prices.length === 0) {
        return null;
    }

    return Math.min(...prices);
}


/* =========================================================
   6. STATUS LABEL
   ========================================================= */

function getStatusLabel(status) {

    if (status === "ongoing") {
        return "ON GOING";
    }

    if (status === "coming") {
        return "COMING SOON";
    }

    if (status === "past") {
        return "PAST EVENT";
    }

    return "EVENT";
}


/* =========================================================
   7. STATUS CLASS
   ========================================================= */

function getStatusClass(status) {

    if (status === "ongoing") {
        return "status-ongoing";
    }

    if (status === "coming") {
        return "status-coming";
    }

    if (status === "past") {
        return "status-past";
    }

    return "";
}


/* =========================================================
   8. CREATE EVENT CARD
========================================================= */

function createEventCard(event) {

    const cheapest =
        getCheapestPrice(event);

    const favorite =
        favoriteEvents.includes(event.id);


    return `
        <article
            class="event-card"
            data-id="${event.id}"
            data-title="${event.title.toLowerCase()}"
            data-category="${event.category.toLowerCase()} ${event.subcategory.toLowerCase()}"
        >

            <div class="event-card-image">

                <img
                    src="${event.image}"
                    alt="${event.title}"
                >

                <span class="event-status ${getStatusClass(event.status)}">
                    ${getStatusLabel(event.status)}
                </span>


                <button
                    class="favorite-button ${favorite ? "active" : ""}"
                    onclick="toggleFavorite('${event.id}', this)"
                >

                    <i class="${favorite ? "fa-solid" : "fa-regular"} fa-heart"></i>

                </button>

            </div>


            <div class="event-card-content">

                <span class="event-card-category">
                    ${event.category.toUpperCase()}
                </span>


                <h3 class="event-card-title">
                    ${event.title}
                </h3>


                <div class="event-card-meta">

                    <span>
                        <i class="fa-regular fa-calendar"></i>
                        ${event.date}
                    </span>


                    <span>
                        <i class="fa-solid fa-location-dot"></i>
                        ${event.location}
                    </span>

                </div>


                <div class="event-card-bottom">

                    <div>

                        <span class="price-label">
                            Mulai dari
                        </span>

                        <strong class="event-card-price">
                            ${cheapest === null ? "Cek harga" : formatRupiah(cheapest)}
                        </strong>

                    </div>


                    <button
                        class="detail-event-button"
                        onclick="openEventDetail('${event.id}')"
                    >

                        Detail

                    </button>

                </div>

            </div>

        </article>
    `;
}


/* =========================================================
   9. RENDER RECOMMENDED
========================================================= */

function renderRecommended() {

    const grid =
        document.getElementById(
            "eventGrid"
        );

    if (!grid) return;


    const recommended =
        events.filter(
            event => event.recommended
        );


    grid.innerHTML =
        recommended
            .map(createEventCard)
            .join("");
}


/* =========================================================
   10. RENDER ONGOING
========================================================= */

function renderOngoing() {

    const grid =
        document.getElementById(
            "ongoingGrid"
        );

    if (!grid) return;


    const ongoing =
        events.filter(
            event =>
                event.status === "ongoing"
        );


    grid.innerHTML =
        ongoing
            .map(createEventCard)
            .join("");
}


/* =========================================================
   11. RENDER COMING
========================================================= */

function renderComing() {

    const grid =
        document.getElementById(
            "comingGrid"
        );

    if (!grid) return;


    const coming =
        events.filter(
            event =>
                event.status === "coming"
        );


    grid.innerHTML =
        coming
            .map(createEventCard)
            .join("");
}


/* =========================================================
   12. RENDER PAST
========================================================= */

function renderPast() {

    const grid =
        document.getElementById(
            "pastGrid"
        );

    if (!grid) return;


    const past =
        events.filter(
            event =>
                event.status === "past"
        );


    grid.innerHTML =
        past
            .map(createEventCard)
            .join("");
}


/* =========================================================
   13. RENDER ALL
========================================================= */

function renderAllEvents() {

    renderRecommended();

    renderOngoing();

    renderComing();

    renderPast();
}


/* =========================================================
   14. SEARCH
========================================================= */

function searchEvents() {

    const input =
        document.getElementById(
            "searchInput"
        );

    if (!input) return;


    const keyword =
        input.value
            .toLowerCase()
            .trim();


    if (!keyword) {

        renderAllEvents();

        return;
    }


    const result =
        events.filter(event => {

            return (

                event.title
                    .toLowerCase()
                    .includes(keyword)

                ||

                event.category
                    .toLowerCase()
                    .includes(keyword)

                ||

                event.subcategory
                    .toLowerCase()
                    .includes(keyword)

                ||

                event.location
                    .toLowerCase()
                    .includes(keyword)

                ||

                event.lineup
                    .join(" ")
                    .toLowerCase()
                    .includes(keyword)

            );

        });


    const recommended =
        result.filter(
            event => event.recommended
        );

    const ongoing =
        result.filter(
            event => event.status === "ongoing"
        );

    const coming =
        result.filter(
            event => event.status === "coming"
        );

    const past =
        result.filter(
            event => event.status === "past"
        );


    document.getElementById(
        "eventGrid"
    ).innerHTML =
        recommended
            .map(createEventCard)
            .join("");


    document.getElementById(
        "ongoingGrid"
    ).innerHTML =
        ongoing
            .map(createEventCard)
            .join("");


    document.getElementById(
        "comingGrid"
    ).innerHTML =
        coming
            .map(createEventCard)
            .join("");


    document.getElementById(
        "pastGrid"
    ).innerHTML =
        past
            .map(createEventCard)
            .join("");


    const emptyState =
        document.getElementById(
            "emptyState"
        );


    if (result.length === 0) {

        emptyState.style.display =
            "block";

    } else {

        emptyState.style.display =
            "none";

    }


    document
        .getElementById("events")
        .scrollIntoView({
            behavior: "smooth"
        });
}


/* =========================================================
   15. SEARCH KEYWORD
========================================================= */

function searchKeyword(keyword) {

    const input =
        document.getElementById(
            "searchInput"
        );

    if (!input) return;


    input.value = keyword;

    searchEvents();
}


/* =========================================================
   16. FILTER CATEGORY
========================================================= */

function filterCategory(category) {

    const normalized =
        category.toLowerCase();


    const result =
        events.filter(event => {

            return (
                event.category
                    .toLowerCase()
                    .includes(normalized)

                ||

                event.subcategory
                    .toLowerCase()
                    .includes(normalized)
            );

        });


    document.getElementById(
        "eventGrid"
    ).innerHTML =
        result
            .filter(
                event =>
                    event.recommended
            )
            .map(createEventCard)
            .join("");


    document.getElementById(
        "ongoingGrid"
    ).innerHTML =
        result
            .filter(
                event =>
                    event.status === "ongoing"
            )
            .map(createEventCard)
            .join("");


    document.getElementById(
        "comingGrid"
    ).innerHTML =
        result
            .filter(
                event =>
                    event.status === "coming"
            )
            .map(createEventCard)
            .join("");


    document.getElementById(
        "pastGrid"
    ).innerHTML =
        result
            .filter(
                event =>
                    event.status === "past"
            )
            .map(createEventCard)
            .join("");


    document.getElementById(
        "emptyState"
    ).style.display =
        result.length === 0
            ? "block"
            : "none";


    document
        .getElementById("events")
        .scrollIntoView({
            behavior: "smooth"
        });
}


/* =========================================================
   17. SHOW ALL
========================================================= */

function showAllEvents() {

    const input =
        document.getElementById(
            "searchInput"
        );

    if (input) {
        input.value = "";
    }


    document.getElementById(
        "emptyState"
    ).style.display = "none";


    renderAllEvents();


    document
        .getElementById("events")
        .scrollIntoView({
            behavior: "smooth"
        });
}


/* =========================================================
   18. EVENT DETAIL
========================================================= */

function openEventDetail(eventId) {

    const event =
        events.find(
            item =>
                item.id === eventId
        );


    if (!event) {

        showToast(
            "Data event tidak ditemukan."
        );

        return;
    }


    currentEvent =
        event;


    document.getElementById(
        "detailEventImage"
    ).src =
        event.image;


    document.getElementById(
        "detailEventCategory"
    ).textContent =
        event.category.toUpperCase();


    document.getElementById(
        "detailEventTitle"
    ).textContent =
        event.title;


    document.getElementById(
        "detailEventDate"
    ).textContent =
        event.date;


    document.getElementById(
        "detailEventLocation"
    ).textContent =
        event.location;


    document.getElementById(
        "detailEventTime"
    ).textContent =
        event.time;


    document.getElementById(
        "detailEventVenue"
    ).textContent =
        event.venue;


    document.getElementById(
        "detailEventDescription"
    ).textContent =
        event.description;


    const lineupList =
        document.getElementById(
            "lineupList"
        );


    lineupList.innerHTML =
        event.lineup
            .map(
                artist =>
                    `<span class="lineup-tag">${artist}</span>`
            )
            .join("");


    renderTicketOptions(event);


    openModal(
        "eventModal"
    );
}


/* =========================================================
   19. RENDER TICKET OPTIONS
========================================================= */

function renderTicketOptions(event) {

    const ticketList =
        document.getElementById(
            "ticketList"
        );


    if (!event.tickets.length) {

        ticketList.innerHTML = `
            <div class="empty-account">

                <i class="fa-solid fa-circle-info"></i>

                <h3>
                    Harga tiket belum tersedia
                </h3>

                <p>
                    Informasi harga akan ditampilkan
                    setelah data tiket tersedia.
                </p>

            </div>
        `;

        return;
    }


    ticketList.innerHTML =
        event.tickets
            .map(
                (ticket, index) => {

                    const disabled =
                        ticket.price === null;


                    return `
                        <button
                            class="ticket-option"
                            onclick="${
                                disabled
                                    ? `showToast('Harga tiket belum tersedia untuk kategori ini.')`
                                    : `selectTicket(${index})`
                            }"
                        >

                            <div class="ticket-option-left">

                                <strong>
                                    ${ticket.name}
                                </strong>

                                <small>
                                    ${ticket.description}
                                </small>

                            </div>


                            <div class="ticket-option-price">

                                ${
                                    ticket.price === null
                                        ? "Cek Harga"
                                        : formatRupiah(ticket.price)
                                }

                            </div>

                        </button>
                    `;
                }
            )
            .join("");
}


/* =========================================================
   20. SELECT TICKET
========================================================= */

function selectTicket(ticketIndex) {

    if (!currentEvent) {

        showToast(
            "Event belum dipilih."
        );

        return;
    }


    const ticket =
        currentEvent.tickets[
            ticketIndex
        ];


    if (!ticket) {

        showToast(
            "Tiket tidak ditemukan."
        );

        return;
    }


    if (ticket.price === null) {

        showToast(
            "Harga tiket belum tersedia."
        );

        return;
    }


    selectedTicket = {
        ...ticket
    };


    selectedSeat = null;


    document.getElementById(
        "selectedTicketText"
    ).textContent =
        `${ticket.name} • ${formatRupiah(ticket.price)}`;


    closeModal(
        "eventModal"
    );


    renderSeatMap(
        currentEvent
    );


    openModal(
        "seatModal"
    );
}


/* =========================================================
   21. GENERATE SEAT MAP
========================================================= */

function renderSeatMap(event) {

    const venueMap =
        document.getElementById(
            "venueMap"
        );


    venueMap.innerHTML = "";


    let seats = [];


    if (
        event.seats &&
        event.seats.length > 0
    ) {

        seats =
            event.seats;

    } else {

        for (
            let row = 1;
            row <= 4;
            row++
        ) {

            for (
                let col = 1;
                col <= 5;
                col++
            ) {

                seats.push(
                    `${String.fromCharCode(64 + row)}0${col}`
                );

            }
        }

    }


    const rows = {};


    seats.forEach(seat => {

        const row =
            seat.match(/[A-Z]+/);


        const rowName =
            row
                ? row[0]
                : "A";


        if (!rows[rowName]) {
            rows[rowName] = [];
        }


        rows[rowName].push(
            seat
        );

    });


    Object.keys(rows).forEach(
        rowName => {

            const rowElement =
                document.createElement(
                    "div"
                );


            rowElement.className =
                "seat-row";


            rows[rowName]
                .forEach(
                    seatNumber => {

                        const seatButton =
                            document.createElement(
                                "button"
                            );


                        seatButton.className =
                            "seat";


                        seatButton.textContent =
                            seatNumber;


                        const sold =
                            Math.random() < 0.15;


                        if (sold) {

                            seatButton.classList.add(
                                "sold"
                            );

                            seatButton.disabled =
                                true;

                        } else {

                            seatButton.onclick =
                                () =>
                                    chooseSeat(
                                        seatButton,
                                        seatNumber
                                    );

                        }


                        rowElement.appendChild(
                            seatButton
                        );

                    }
                );


            venueMap.appendChild(
                rowElement
            );

        }
    );


    document.getElementById(
        "selectedSeatDisplay"
    ).textContent =
        "-";


    document.getElementById(
        "continueSeatButton"
    ).disabled =
        true;
}


/* =========================================================
   22. CHOOSE SEAT
========================================================= */

function chooseSeat(
    button,
    seatNumber
) {

    document
        .querySelectorAll(
            "#venueMap .seat.selected"
        )
        .forEach(
            seat =>
                seat.classList.remove(
                    "selected"
                )
        );


    button.classList.add(
        "selected"
    );


    selectedSeat =
        seatNumber;


    document.getElementById(
        "selectedSeatDisplay"
    ).textContent =
        `${selectedTicket.name} - ${seatNumber}`;


    document.getElementById(
        "continueSeatButton"
    ).disabled =
        false;
}


/* =========================================================
   23. CHECKOUT
========================================================= */

function continueToCheckout() {

    if (!selectedTicket) {

        showToast(
            "Pilih tiket terlebih dahulu."
        );

        return;
    }


    if (!selectedSeat) {

        showToast(
            "Pilih seat terlebih dahulu."
        );

        return;
    }


    const price =
        selectedTicket.price;


    const fee =
        Math.round(
            price * 0.02
        );


    const total =
        price + fee;


    document.getElementById(
        "checkoutEvent"
    ).textContent =
        currentEvent.title;


    document.getElementById(
        "checkoutTicket"
    ).textContent =
        selectedTicket.name;


    document.getElementById(
        "checkoutSeat"
    ).textContent =
        selectedSeat;


    document.getElementById(
        "checkoutPrice"
    ).textContent =
        formatRupiah(price);


    document.getElementById(
        "checkoutFee"
    ).textContent =
        formatRupiah(fee);


    document.getElementById(
        "checkoutTotal"
    ).textContent =
        formatRupiah(total);


    selectPaymentMethod(
        "QRIS"
    );


    closeModal(
        "seatModal"
    );


    openModal(
        "checkoutModal"
    );
}


/* =========================================================
   24. PAYMENT METHOD
========================================================= */

function selectPaymentMethod(method) {

    selectedPaymentMethod = method;

    const detail =
        document.getElementById("paymentDetail");

    if (!detail) return;


    /* =====================================================
       QRIS
    ===================================================== */

    if (method === "QRIS") {

        detail.innerHTML = `
            <div class="payment-detail-box">

                <strong>
                    QRIS
                </strong>

                <br><br>

                Setelah klik
                <strong>Bayar Sekarang</strong>,
                sistem akan menampilkan QR
                pembayaran.

            </div>
        `;

    }


    /* =====================================================
       E-WALLET
    ===================================================== */

    else if (method === "E-Wallet") {

        detail.innerHTML = `
            <div class="payment-detail-box">

                <strong>
                    E-Wallet
                </strong>

                <br><br>

                Pilihan pembayaran:

                <br><br>

                • GoPay
                <br>

                • DANA
                <br>

                • OVO

                <br><br>

                Pilih e-wallet yang tersedia
                pada halaman pembayaran.

            </div>
        `;

    }


    /* =====================================================
       BANK TRANSFER
    ===================================================== */

    else if (method === "Bank Transfer") {

        const vaNumber =
            generateVANumber();

        detail.innerHTML = `
            <div class="payment-detail-box">

                <strong>
                    Virtual Account
                </strong>

                <br><br>

                Bank:

                <strong>
                    BCA Virtual Account
                </strong>

                <br><br>

                Nomor VA:

                <strong>
                    ${vaNumber}
                </strong>

                <br><br>

                Nominal:

                <strong>
                    ${formatRupiah(
                        selectedTicket
                            ? selectedTicket.price +
                              Math.round(
                                  selectedTicket.price * 0.02
                              )
                            : 0
                    )}
                </strong>

                <br><br>

                Status:

                <strong>
                    Menunggu Pembayaran
                </strong>

            </div>
        `;

    }


    /* =====================================================
       CREDIT / DEBIT CARD
    ===================================================== */

    else if (method === "Credit Card") {

        detail.innerHTML = `
            <div class="payment-detail-box">

                <strong>
                    Credit / Debit Card
                </strong>

                <br><br>

                Mendukung:

                <br>

                • Visa
                <br>

                • Mastercard

                <br><br>

                Pembayaran kartu akan
                diproses melalui payment gateway.

            </div>
        `;

    }

}


/* =========================================================
   GENERATE VIRTUAL ACCOUNT
========================================================= */

function generateVANumber() {

    const random =
        Math.floor(
            100000000000 +
            Math.random() * 900000000000
        );

    return random.toString();
}

/* =========================================================
   25. PROCESS PAYMENT
========================================================= */

function processPayment() {

    if (!currentEvent) {

        showToast(
            "Event tidak ditemukan."
        );

        return;
    }


    if (!selectedTicket) {

        showToast(
            "Tiket belum dipilih."
        );

        return;
    }


    if (!selectedSeat) {

        showToast(
            "Seat belum dipilih."
        );

        return;
    }


    if (
        selectedPaymentMethod ===
        "QRIS"
    ) {

        createQRIS();

        closeModal(
            "checkoutModal"
        );

        openModal(
            "qrisModal"
        );

        return;
    }


    showToast(
        `Menyiapkan pembayaran ${selectedPaymentMethod}...`
    );


    setTimeout(
        () => {

            createSuccessfulOrder();

        },
        1200
    );
}


/* =========================================================
   26. CREATE QRIS
========================================================= */

function createQRIS() {

    const qrisContainer =
        document.getElementById(
            "qrisCode"
        );


    qrisContainer.innerHTML = "";


    generateGridQR(
        qrisContainer,
        21
    );


    const total =
        selectedTicket.price +
        Math.round(
            selectedTicket.price * 0.02
        );


    document.getElementById(
        "qrisAmount"
    ).textContent =
        formatRupiah(total);
}


/* =========================================================
   27. SIMULATE SUCCESS
========================================================= */

function simulatePaymentSuccess() {

    closeModal(
        "qrisModal"
    );


    showToast(
        "Memverifikasi pembayaran..."
    );


    setTimeout(
        () => {

            createSuccessfulOrder();

        },
        1000
    );
}


/* =========================================================
   28. CREATE SUCCESSFUL ORDER
========================================================= */

function createSuccessfulOrder() {

    const random =
        Math.floor(
            100000 +
            Math.random() * 900000
        );


    const orderId =
        `TIX-${random}`;


    const ticketCode =
        `TIX-${Date.now()}`;


    const fee =
        Math.round(
            selectedTicket.price * 0.02
        );


    const total =
        selectedTicket.price +
        fee;


    currentOrder = {

        orderId,

        ticketCode,

        userEmail:
            currentUser
                ? currentUser.email
                : "guest@tixora.id",

        eventId:
            currentEvent.id,

        eventName:
            currentEvent.title,

        date:
            currentEvent.date,

        venue:
            currentEvent.venue,

        ticketType:
            selectedTicket.name,

        seat:
            selectedSeat,

        price:
            selectedTicket.price,

        fee,

        total,

        paymentMethod:
            selectedPaymentMethod,

        status:
            "PAID",

        checkedIn:
            false,

        createdAt:
            new Date().toLocaleString(
                "id-ID"
            )

    };


    orders.push(
        currentOrder
    );


    localStorage.setItem(
        "tixoraOrders",
        JSON.stringify(orders)
    );


    document.getElementById(
        "successOrderId"
    ).textContent =
        orderId;


    updateAccountData();


    openModal(
        "successModal"
    );
}


/* =========================================================
   29. DIGITAL TICKET
========================================================= */

function openMyTicket() {

    if (!currentOrder) {

        showToast(
            "Belum ada tiket."
        );

        return;
    }


    closeModal(
        "successModal"
    );


    document.getElementById(
        "ticketEventName"
    ).textContent =
        currentOrder.eventName;


    document.getElementById(
        "ticketDate"
    ).textContent =
        currentOrder.date;


    document.getElementById(
        "ticketVenue"
    ).textContent =
        currentOrder.venue;


    document.getElementById(
        "ticketType"
    ).textContent =
        currentOrder.ticketType;


    document.getElementById(
        "ticketSeat"
    ).textContent =
        currentOrder.seat;


    document.getElementById(
        "ticketCode"
    ).textContent =
        currentOrder.ticketCode;


    generateTicketQR();


    openModal(
        "ticketModal"
    );
}


/* =========================================================
   30. TICKET QR
========================================================= */

function generateTicketQR() {

    const container =
        document.getElementById(
            "ticketQRCode"
        );


    if (!container) return;


    container.innerHTML = "";


    generateGridQR(
        container,
        19
    );
}


/* =========================================================
   31. GENERATE VISUAL QR
========================================================= */

function generateGridQR(
    container,
    size
) {

    container.innerHTML = "";


    const total =
        size * size;


    for (
        let i = 0;
        i < total;
        i++
    ) {

        const cell =
            document.createElement(
                "span"
            );


        const row =
            Math.floor(
                i / size
            );


        const col =
            i % size;


        let black =
            Math.random() > 0.52;


        if (
            isFinder(
                row,
                col,
                0,
                0,
                size
            )
        ) {

            black =
                finderValue(
                    row,
                    col,
                    0,
                    0
                );

        }


        if (
            isFinder(
                row,
                col,
                0,
                size - 7,
                size
            )
        ) {

            black =
                finderValue(
                    row,
                    col,
                    0,
                    size - 7
                );

        }


        if (
            isFinder(
                row,
                col,
                size - 7,
                0,
                size
            )
        ) {

            black =
                finderValue(
                    row,
                    col,
                    size - 7,
                    0
                );

        }


        cell.style.background =
            black
                ? "#111"
                : "#fff";


        container.appendChild(
            cell
        );

    }

}


/* =========================================================
   32. QR FINDER
========================================================= */

function isFinder(
    row,
    col,
    startRow,
    startCol,
    size
) {

    return (

        row >= startRow &&

        row <
            startRow + 7 &&

        col >= startCol &&

        col <
            startCol + 7

    );
}


/* =========================================================
   33. QR FINDER VALUE
========================================================= */

function finderValue(
    row,
    col,
    startRow,
    startCol
) {

    const r =
        row - startRow;

    const c =
        col - startCol;


    if (
        r === 0 ||
        r === 6 ||
        c === 0 ||
        c === 6
    ) {

        return true;

    }


    if (
        r >= 2 &&
        r <= 4 &&
        c >= 2 &&
        c <= 4
    ) {

        return true;

    }


    return false;
}


/* =========================================================
   34. DOWNLOAD TICKET
========================================================= */

function downloadTicket() {

    if (!currentOrder) {

        showToast(
            "Tidak ada tiket untuk di-download."
        );

        return;
    }


    showToast(
        "Fitur download PDF akan diaktifkan pada tahap backend."
    );
}


/* =========================================================
   35. FAVORITE
========================================================= */

function toggleFavorite(
    eventId,
    button
) {

    const index =
        favoriteEvents.indexOf(
            eventId
        );


    if (index === -1) {

        favoriteEvents.push(
            eventId
        );

        button.classList.add(
            "active"
        );


        const icon =
            button.querySelector("i");


        icon.classList.remove(
            "fa-regular"
        );


        icon.classList.add(
            "fa-solid"
        );


        showToast(
            "Event ditambahkan ke Favorite."
        );

    }

    else {

        favoriteEvents.splice(
            index,
            1
        );


        button.classList.remove(
            "active"
        );


        const icon =
            button.querySelector("i");


        icon.classList.remove(
            "fa-solid"
        );


        icon.classList.add(
            "fa-regular"
        );


        showToast(
            "Event dihapus dari Favorite."
        );

    }


    localStorage.setItem(
        "tixoraFavorites",
        JSON.stringify(
            favoriteEvents
        )
    );
}


/* =========================================================
   36. MODAL OPEN
========================================================= */

function openModal(id) {

    const modal =
        document.getElementById(
            id
        );


    if (!modal) return;


    modal.classList.add(
        "active"
    );


    document.body.style.overflow =
        "hidden";
}


/* =========================================================
   37. MODAL CLOSE
========================================================= */

function closeModal(id) {

    const modal =
        document.getElementById(
            id
        );


    if (!modal) return;


    modal.classList.remove(
        "active"
    );


    const activeModals =
        document.querySelectorAll(
            ".modal.active"
        );


    if (
        activeModals.length === 0
    ) {

        document.body.style.overflow =
            "";

    }
}


/* =========================================================
   38. LOGIN MODAL
========================================================= */

function openLoginModal() {

    closeModal(
        "registerModal"
    );


    openModal(
        "loginModal"
    );
}


/* =========================================================
   39. REGISTER MODAL
========================================================= */

function openRegisterModal() {

    closeModal(
        "loginModal"
    );


    openModal(
        "registerModal"
    );
}


/* =========================================================
   40. REGISTER USER
========================================================= */

function registerUser(event) {

    event.preventDefault();


    const name =
        document.getElementById(
            "registerName"
        ).value.trim();


    const email =
        document.getElementById(
            "registerEmail"
        ).value.trim();


    const password =
        document.getElementById(
            "registerPassword"
        ).value;


    const confirmPassword =
        document.getElementById(
            "registerConfirmPassword"
        ).value;


    if (
        password !==
        confirmPassword
    ) {

        showToast(
            "Konfirmasi password tidak sama."
        );

        return;
    }


    const existing =
        registeredUsers.find(
            user =>
                user.email === email
        );


    if (existing) {

        showToast(
            "Email sudah terdaftar."
        );

        return;
    }


    const user = {

        name,

        email,

        password

    };


    registeredUsers.push(
        user
    );


    localStorage.setItem(
        "tixoraUsers",
        JSON.stringify(
            registeredUsers
        )
    );


    currentUser =
        user;


    saveCurrentUser();


    updateProfileUI();


    closeModal(
        "registerModal"
    );


    showToast(
        `Selamat datang, ${name}!`
    );


    document.querySelector(
        ".auth-form"
    )?.reset();

}


/* =========================================================
   41. LOGIN USER
========================================================= */

function loginUser(event) {

    event.preventDefault();


    const email =
        document.getElementById(
            "loginEmail"
        ).value.trim();


    const password =
        document.getElementById(
            "loginPassword"
        ).value;


    const user =
        registeredUsers.find(
            item =>
                item.email === email &&
                item.password === password
        );


    if (!user) {

        showToast(
            "Email atau password salah."
        );

        return;
    }


    currentUser =
        user;


    saveCurrentUser();


    updateProfileUI();


    closeModal(
        "loginModal"
    );


    showToast(
        `Login berhasil. Selamat datang, ${user.name}!`
    );
}


/* =========================================================
   42. CURRENT USER STORAGE
========================================================= */

function saveCurrentUser() {

    if (currentUser) {

        localStorage.setItem(
            "tixoraCurrentUser",
            JSON.stringify(
                currentUser
            )
        );

    }

}


/* =========================================================
   43. LOAD USER
========================================================= */

function loadCurrentUser() {

    const stored =
        localStorage.getItem(
            "tixoraCurrentUser"
        );


    if (!stored) return;


    try {

        currentUser =
            JSON.parse(
                stored
            );


        updateProfileUI();

    }

    catch {

        localStorage.removeItem(
            "tixoraCurrentUser"
        );

    }
}


/* =========================================================
   44. UPDATE PROFILE UI
========================================================= */

function updateProfileUI() {

    const loginButton =
        document.getElementById(
            "loginButton"
        );


    const registerButton =
        document.getElementById(
            "registerButton"
        );


    const profileWrapper =
        document.getElementById(
            "profileWrapper"
        );


    if (!loginButton ||
        !registerButton ||
        !profileWrapper
    ) {

        return;

    }


    if (currentUser) {

        loginButton.style.display =
            "none";


        registerButton.style.display =
            "none";


        profileWrapper.style.display =
            "block";


        document.getElementById(
            "profileName"
        ).textContent =
            currentUser.name;


        document.getElementById(
            "dropdownName"
        ).textContent =
            currentUser.name;


        document.getElementById(
            "dropdownEmail"
        ).textContent =
            currentUser.email;


        document.getElementById(
            "profileModalName"
        ).textContent =
            currentUser.name;


        document.getElementById(
            "profileModalEmail"
        ).textContent =
            currentUser.email;

    }

    else {

        loginButton.style.display =
            "";


        registerButton.style.display =
            "";


        profileWrapper.style.display =
            "none";

    }

}


/* =========================================================
   45. PROFILE DROPDOWN
========================================================= */

function toggleProfileMenu() {

    const dropdown =
        document.getElementById(
            "profileDropdown"
        );


    dropdown.classList.toggle(
        "active"
    );
}


/* =========================================================
   46. CLOSE PROFILE OUTSIDE
========================================================= */

document.addEventListener(
    "click",
    function(event) {

        const wrapper =
            document.getElementById(
                "profileWrapper"
            );


        const dropdown =
            document.getElementById(
                "profileDropdown"
            );


        if (
            wrapper &&
            dropdown &&
            !wrapper.contains(
                event.target
            )
        ) {

            dropdown.classList.remove(
                "active"
            );

        }

    }
);


/* =========================================================
   47. PROFILE MODAL
========================================================= */

function openProfileModal() {

    document
        .getElementById(
            "profileDropdown"
        )
        ?.classList.remove(
            "active"
        );


    if (!currentUser) {

        openLoginModal();

        return;
    }


    updateProfileUI();


    openModal(
        "profileModal"
    );
}


/* =========================================================
   48. ORDERS
========================================================= */

function openOrdersModal() {

    document
        .getElementById(
            "profileDropdown"
        )
        ?.classList.remove(
            "active"
        );


    if (!currentUser) {

        openLoginModal();

        return;
    }


    renderOrders();


    openModal(
        "ordersModal"
    );
}


/* =========================================================
   49. RENDER ORDERS
========================================================= */

function renderOrders() {

    const container =
        document.getElementById(
            "ordersContent"
        );


    if (!container) return;


    const userOrders =
        orders.filter(
            order =>
                !currentUser ||
                order.userEmail ===
                    currentUser.email
        );


    if (
        userOrders.length === 0
    ) {

        container.innerHTML = `

            <div class="empty-account">

                <i class="fa-solid fa-receipt"></i>

                <h3>
                    Belum ada pesanan
                </h3>

                <p>
                    Pesanan kamu akan muncul
                    setelah melakukan pembelian.
                </p>

            </div>

        `;

        return;
    }


    container.innerHTML =
        userOrders
            .slice()
            .reverse()
            .map(
                order => `

                    <div
                        style="
                            border:1px solid #ebebef;
                            border-radius:12px;
                            padding:14px;
                            margin-bottom:9px;
                        "
                    >

                        <div
                            style="
                                display:flex;
                                justify-content:space-between;
                                gap:10px;
                            "
                        >

                            <strong
                                style="font-size:11px;"
                            >
                                ${order.eventName}
                            </strong>

                            <span
                                style="
                                    color:#2da24a;
                                    font-size:9px;
                                    font-weight:800;
                                "
                            >
                                ${order.status}
                            </span>

                        </div>


                        <div
                            style="
                                margin-top:8px;
                                color:#85858d;
                                font-size:9px;
                                line-height:1.7;
                            "
                        >

                            Order:
                            <strong style="color:#222;">
                                ${order.orderId}
                            </strong>

                            <br>

                            Ticket:
                            ${order.ticketType}

                            <br>

                            Seat:
                            ${order.seat}

                            <br>

                            Payment:
                            ${order.paymentMethod}

                            <br>

                            Total:
                            <strong style="color:#7257ff;">
                                ${formatRupiah(order.total)}
                            </strong>

                        </div>

                    </div>

                `
            )
            .join("");
}


/* =========================================================
   50. TICKETS
========================================================= */

function openTicketsModal() {

    document
        .getElementById(
            "profileDropdown"
        )
        ?.classList.remove(
            "active"
        );


    if (!currentUser) {

        openLoginModal();

        return;
    }


    renderTickets();


    openModal(
        "ticketsModal"
    );
}


/* =========================================================
   51. RENDER TICKETS
========================================================= */

function renderTickets() {

    const container =
        document.getElementById(
            "ticketHistory"
        );


    if (!container) return;


    const userTickets =
        orders.filter(
            order =>
                order.userEmail ===
                currentUser.email &&
                order.status ===
                "PAID"
        );


    if (
        userTickets.length === 0
    ) {

        container.innerHTML = `

            <div class="empty-account">

                <i class="fa-solid fa-ticket"></i>

                <h3>
                    Belum ada tiket
                </h3>

                <p>
                    Tiket akan muncul setelah
                    pembayaran berhasil.
                </p>

            </div>

        `;

        return;
    }


    container.innerHTML =
        userTickets
            .slice()
            .reverse()
            .map(
                order => `

                    <button
                        onclick="openStoredTicket('${order.ticketCode}')"
                        style="
                            width:100%;
                            border:1px solid #e8e8ed;
                            background:white;
                            border-radius:12px;
                            padding:14px;
                            margin-bottom:9px;
                            text-align:left;
                        "
                    >

                        <strong
                            style="
                                display:block;
                                font-size:11px;
                            "
                        >
                            ${order.eventName}
                        </strong>


                        <span
                            style="
                                display:block;
                                margin-top:5px;
                                color:#888;
                                font-size:9px;
                            "
                        >
                            ${order.ticketType}
                            •
                            ${order.seat}
                        </span>


                        <span
                            style="
                                display:block;
                                margin-top:5px;
                                color:#7257ff;
                                font-size:9px;
                                font-weight:800;
                            "
                        >
                            ${order.ticketCode}
                        </span>

                    </button>

                `
            )
            .join("");
}


/* =========================================================
   52. OPEN STORED TICKET
========================================================= */

function openStoredTicket(
    ticketCode
) {

    const order =
        orders.find(
            item =>
                item.ticketCode ===
                ticketCode
        );


    if (!order) {

        showToast(
            "Tiket tidak ditemukan."
        );

        return;
    }


    currentOrder =
        order;


    closeModal(
        "ticketsModal"
    );


    openMyTicket();
}


/* =========================================================
   53. LOGOUT
========================================================= */

function logoutUser() {

    currentUser = null;


    localStorage.removeItem(
        "tixoraCurrentUser"
    );


    document
        .getElementById(
            "profileDropdown"
        )
        ?.classList.remove(
            "active"
        );


    updateProfileUI();


    showToast(
        "Kamu telah logout."
    );
}


/* =========================================================
   54. LOCATION
========================================================= */

function openLocationModal() {

    openModal(
        "locationModal"
    );
}


/* =========================================================
   55. SELECT LOCATION
========================================================= */

function selectLocation(
    location
) {

    document.getElementById(
        "selectedLocation"
    ).textContent =
        location;


    closeModal(
        "locationModal"
    );


    showToast(
        `Lokasi ${location} dipilih.`
    );


    filterLocation(
        location
    );
}


/* =========================================================
   56. FILTER LOCATION
========================================================= */

function filterLocation(
    location
) {

    if (
        location ===
        "JABODETABEK"
    ) {

        renderAllEvents();

        return;
    }


    const result =
        events.filter(
            event =>
                event.location
                    .toLowerCase()
                    .includes(
                        location.toLowerCase()
                    )
        );


    document.getElementById(
        "eventGrid"
    ).innerHTML =
        result
            .map(createEventCard)
            .join("");


    document.getElementById(
        "ongoingGrid"
    ).innerHTML = "";


    document.getElementById(
        "comingGrid"
    ).innerHTML = "";


    document.getElementById(
        "pastGrid"
    ).innerHTML = "";


    document.getElementById(
        "emptyState"
    ).style.display =
        result.length === 0
            ? "block"
            : "none";


    document
        .getElementById(
            "events"
        )
        .scrollIntoView({
            behavior: "smooth"
        });
}


/* =========================================================
   57. MOBILE MENU
========================================================= */

function toggleMobileMenu() {

    document
        .getElementById(
            "mobileNavigation"
        )
        ?.classList.toggle(
            "active"
        );
}


/* =========================================================
   58. CLOSE MOBILE
========================================================= */

function closeMobileMenu() {

    document
        .getElementById(
            "mobileNavigation"
        )
        ?.classList.remove(
            "active"
        );
}


/* =========================================================
   59. TOP
========================================================= */

function scrollToTop() {

    window.scrollTo({

        top: 0,

        behavior: "smooth"

    });
}


/* =========================================================
   60. TOAST
========================================================= */

function showToast(
    message
) {

    const toast =
        document.getElementById(
            "toast"
        );


    const text =
        document.getElementById(
            "toastMessage"
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
        window.tixoraToastTimer
    );


    window.tixoraToastTimer =
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
   61. SWITCH LOGIN
========================================================= */

function switchToLogin() {

    closeModal(
        "registerModal"
    );


    openLoginModal();
}


/* =========================================================
   62. SWITCH REGISTER
========================================================= */

function switchToRegister() {

    closeModal(
        "loginModal"
    );


    openRegisterModal();
}


/* =========================================================
   63. UPDATE ACCOUNT
========================================================= */

function updateAccountData() {

    if (currentUser) {

        renderOrders();

        renderTickets();

    }

}


/* =========================================================
   64. CLOSE WITH ESC
========================================================= */

document.addEventListener(
    "keydown",
    function(event) {

        if (
            event.key ===
            "Escape"
        ) {

            document
                .querySelectorAll(
                    ".modal.active"
                )
                .forEach(
                    modal =>
                        modal.classList.remove(
                            "active"
                        )
                );


            document.body.style.overflow =
                "";

        }

    }
);


/* =========================================================
   65. SEARCH ENTER
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    function() {

        const searchInput =
            document.getElementById(
                "searchInput"
            );


        if (searchInput) {

            searchInput.addEventListener(
                "keydown",
                function(event) {

                    if (
                        event.key ===
                        "Enter"
                    ) {

                        event.preventDefault();

                        searchEvents();

                    }

                }
            );

        }

    }
);


/* =========================================================
   66. LOAD APP
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    function() {

        loadCurrentUser();

        renderAllEvents();

        selectPaymentMethod(
            "QRIS"
        );

    }
);