import { FormEvent, useEffect, useState } from "react";

type CmsItem = {
  id: number;
  title: string;
  subtitle: string | null;
  description: string | null;
  image: string | null;
  sort_order: number;
};

declare global {
  interface Window {
    __TECHTONIC_CONTENT__?: Record<string, CmsItem[]>;
    __TECHTONIC_PAGE_CONTENT__?: Record<string, string>;
  }
}

const cms = window.__TECHTONIC_CONTENT__ ?? {};
const pageContent = window.__TECHTONIC_PAGE_CONTENT__ ?? {};

const editable = (key: string, fallback: string) => pageContent[key] ?? fallback;
const pixelSetting = (key: string, fallback: number) => {
  const value = Number(editable(key, String(fallback)));
  return Number.isFinite(value) ? Math.min(400, Math.max(100, value)) : fallback;
};
const headingLines = (value: string) =>
  value.split(/\r?\n/).map((line, index) => (
    <span key={`${line}-${index}`}>
      {index > 0 && <br />}
      {line}
    </span>
  ));
const contentLines = (value: string) => value.split(/\r?\n/).map((line) => line.trim()).filter(Boolean);
const phoneHref = (phone: string) => `tel:${phone.replace(/[^\d+]/g, "")}`;

const serviceLinks = [
  { label: editable("nav_facilities", "Facilities"), href: "#facilities" },
  { label: editable("nav_equipment", "Equipment"), href: "#equipment" },
  { label: editable("nav_products", "Products"), href: "#products" },
];

const fallbackRegistry = [
  { title: "SEC Registration", note: "Certificate of Incorporation", image: "/images/registry-sec.webp" },
  { title: "BIR Registration", note: "Registered business entity", image: "/images/registry-bir.webp" },
  { title: "Laboratory Accreditation", note: "Independent testing recognition", image: "/images/registry-accreditation.webp" },
  { title: "Calibration Certificates", note: "Verified batching accuracy", image: "/images/registry-calibration.webp" },
];

const fallbackLeaders = [
  { name: "Francis Victor R. Lamata", role: "CEO / President", initials: "FL" },
  { name: "Renand T. Yutis", role: "General Manager", initials: "RY" },
  { name: "Herbert A. Capangyarihan", role: "Batching Plant Manager", initials: "HC" },
  { name: "Ismael P. Fuentes", role: "QC Supervisor", initials: "IF" },
];

const fallbackOwners = [
  ["Angelo Gabriel R. Lamata", "Corporate Secretary"],
  ["Jose Nilbert R. Lamata", "Treasurer"],
  ["Inna Concepcion R. Lamata", "Board of Director"],
  ["Adrian Joshua R. Lamata", "Board of Director"],
  ["Fatima Trisha R. Lamata", "Board of Director"],
  ["Wynona Daniella R. Lamata", "Board of Director"],
];

const fallbackFacilities = [
  { name: "Batching Plant", image: "/images/facility-batching.webp", detail: "Computerized wet-mix production" },
  { name: "Control Room", image: "/images/facility-control.webp", detail: "Accurate and monitored batching" },
  { name: "Laboratory", image: "/images/facility-laboratory.webp", detail: "On-site quality control" },
  { name: "Cement Warehouse", image: "/images/facility-warehouse.webp", detail: "Organized materials storage" },
];

const fallbackEquipment = [
  { name: "Transit Mixer Fleet", image: "/images/equipment-mixer.webp", detail: "6 and 10 cu. m. units" },
  { name: "Concrete Pumps", image: "/images/equipment-pump.webp", detail: "Truck-mounted placement reach" },
  { name: "Payloader", image: "/images/equipment-loader.webp", detail: "Reliable yard operations" },
  { name: "Heavy Equipment", image: "/images/equipment-fleet.webp", detail: "Project-ready support fleet" },
];

const fallbackProjects = [
  { name: "SMDC - Smile Residences", image: "/images/project-smile.webp", place: "Bacolod City" },
  { name: "Citadines Hotel", image: "/images/project-citadines.webp", place: "Bacolod City" },
  { name: "J. Qua Construction", image: "/images/project-jqua.webp", place: "Negros Occidental" },
  { name: "URC", image: "/images/project-urc.webp", place: "Kabankalan" },
];

const fallbackProducts = [
  { title: "Quality Ready-Mixed Concrete", description: "Consistent concrete supply for roads, bridges, malls, buildings, and other developments." },
  { title: "Controlled Mix Production", description: "Computerized wet-mix batching supports repeatable proportions, reliable output, and specification accuracy." },
  { title: "High-Volume Project Supply", description: "Up to 90 cubic meters per hour of plant capacity, backed by a coordinated mixer and pump fleet." },
];

const registryItems = (cms.registry ?? []).map((item) => ({
  title: item.title,
  note: item.subtitle ?? "",
  image: item.image ?? "",
}));
const SHOW_BUSINESS_REGISTRY = registryItems.length > 0;

const cmsTeam = cms.team ?? [];
const leaders = cmsTeam.length
  ? cmsTeam.filter((item) => item.description !== "BOARD").slice(0, 4).map((item) => ({
      name: item.title,
      role: item.subtitle ?? "",
      initials: item.description || item.title.split(" ").slice(0, 2).map((word) => word[0]).join(""),
      image: item.image,
    }))
  : fallbackLeaders.map((leader) => ({ ...leader, image: null }));
const owners: [string, string][] = cmsTeam.length
  ? cmsTeam.filter((item) => item.description === "BOARD").map((item) => [item.title, item.subtitle ?? ""])
  : fallbackOwners.map((item) => [item[0], item[1]]);
const facilities = (cms.facility ?? []).length
  ? cms.facility.map((item) => ({ name: item.title, image: item.image ?? "", detail: item.subtitle ?? "" }))
  : fallbackFacilities;
const equipment = (cms.equipment ?? []).length
  ? cms.equipment.map((item) => ({ name: item.title, image: item.image ?? "", detail: item.subtitle ?? "" }))
  : fallbackEquipment;
const projects = (cms.project ?? []).length
  ? cms.project.map((item) => ({ name: item.title, image: item.image ?? "", place: item.subtitle ?? "" }))
  : fallbackProjects;
const products = (cms.product ?? []).length
  ? cms.product.map((item) => ({ title: item.title, description: item.description ?? "" }))
  : fallbackProducts;

export default function Home() {
  const [menuOpen, setMenuOpen] = useState(false);
  const [servicesOpen, setServicesOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [inquiryOpen, setInquiryOpen] = useState(false);
  const [sending, setSending] = useState(false);
  const [formMessage, setFormMessage] = useState("");
  const contactAddress = contentLines(editable("contact_address", "Purok Paho, Brgy. Felisa\nBacolod City, Negros Occidental\nPhilippines 6100"));
  const contactTelephones = contentLines(editable("contact_telephones", "034-213-0490\n034-461-9194"));
  const contactMobiles = contentLines(editable("contact_mobiles", "0998-476-2210\n0918-664-0085\n0936-923-3732"));
  const contactEmail = editable("contact_email", "techtonicrmc@gmail.com");
  const headerLogoWidth = pixelSetting("header_logo_width", 252);
  const footerLogoWidth = pixelSetting("footer_logo_width", 205);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    if (!menuOpen) return;

    const previousOverflow = document.body.style.overflow;
    const closeOnEscape = (event: KeyboardEvent) => {
      if (event.key === "Escape") {
        setMenuOpen(false);
        setServicesOpen(false);
      }
    };

    document.body.style.overflow = "hidden";
    window.addEventListener("keydown", closeOnEscape);

    return () => {
      document.body.style.overflow = previousOverflow;
      window.removeEventListener("keydown", closeOnEscape);
    };
  }, [menuOpen]);

  useEffect(() => {
    if (!inquiryOpen) return;
    const previousOverflow = document.body.style.overflow;
    const closeOnEscape = (event: KeyboardEvent) => {
      if (event.key === "Escape") setInquiryOpen(false);
    };
    document.body.style.overflow = "hidden";
    window.addEventListener("keydown", closeOnEscape);
    return () => {
      document.body.style.overflow = previousOverflow;
      window.removeEventListener("keydown", closeOnEscape);
    };
  }, [inquiryOpen]);

  const closeMenus = () => {
    setMenuOpen(false);
    setServicesOpen(false);
  };

  const submitInquiry = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setSending(true);
    setFormMessage("");
    const form = event.currentTarget;
    const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? "";

    try {
      const response = await fetch("/contact", {
        method: "POST",
        headers: { Accept: "application/json", "X-CSRF-TOKEN": token },
        body: new FormData(form),
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || editable("inquiry_error_message", "Unable to send your inquiry. Please check your details and try again."));
      form.reset();
      setFormMessage(data.message);
    } catch (error) {
      setFormMessage(error instanceof Error ? error.message : editable("inquiry_error_message", "Unable to send your inquiry. Please check your details and try again."));
    } finally {
      setSending(false);
    }
  };

  return (
    <main>
      <header className={`site-header ${scrolled ? "is-scrolled" : ""}`}>
        <a className="brand" href="#home" aria-label="Techtonic home" style={{ width: `${headerLogoWidth}px` }}>
          <img src={editable("header_logo_image", "/images/techtonic-logo-white.png")} alt="Techtonic Concrete Industries Inc." />
        </a>

        <button
          className={`menu-toggle ${menuOpen ? "is-open" : ""}`}
          type="button"
          aria-label={menuOpen ? "Close navigation" : "Open navigation"}
          aria-expanded={menuOpen}
          aria-controls="primary-navigation"
          onClick={() => {
            setMenuOpen((open) => !open);
            setServicesOpen(false);
          }}
        >
          <span />
          <span />
        </button>

        <nav
          id="primary-navigation"
          className={`main-nav ${menuOpen ? "is-open" : ""}`}
          aria-label="Primary navigation"
        >
          <span className="mobile-menu-label">{editable("navigation_label", "Navigation")}</span>
          <a href="#about" onClick={closeMenus}>{editable("nav_about", "About Us")}</a>
          <a href="#mission" onClick={closeMenus}>{editable("nav_mission", "Mission & Vision")}</a>
          {SHOW_BUSINESS_REGISTRY && (
            <a href="#registry" onClick={closeMenus}>{editable("nav_registry", "Business Registry")}</a>
          )}
          <a href="#team" onClick={closeMenus}>{editable("nav_team", "Our Team")}</a>
          <div className={`services-menu ${servicesOpen ? "is-open" : ""}`}>
            <button
              type="button"
              aria-expanded={servicesOpen}
              aria-haspopup="true"
              onClick={() => setServicesOpen((open) => !open)}
            >
              {editable("nav_services", "Services")} <span className="chevron" aria-hidden="true" />
            </button>
            <div className="services-dropdown">
              {serviceLinks.map((item) => (
                <a key={item.label} href={item.href} onClick={closeMenus}>
                  <span>{item.label}</span>
                  <span aria-hidden="true">↗</span>
                </a>
              ))}
            </div>
          </div>
          <a className="nav-contact" href="#contact" onClick={closeMenus}>{editable("nav_contact", "Contact Us")}</a>
        </nav>
      </header>

      <section className="hero" id="home">
        <div className="hero-media" style={{ backgroundImage: `url(${editable("hero_background_image", "/images/hero-night.webp")})` }} aria-hidden="true" />
        <div className="hero-shade" aria-hidden="true" />
        <div className="hero-content">
          <p className="eyebrow">{editable("hero_eyebrow", "Built for what comes next")}</p>
          <h1>{headingLines(editable("hero_heading", "Engineering Strength.\nDelivering Certainty."))}</h1>
          <p className="hero-copy">
            {editable("hero_copy", "Reliable ready-mixed concrete, engineered for enduring projects across Bacolod City and Negros Occidental.")}
          </p>
          <div className="hero-actions">
            <a className="button button-primary" href="#facilities">
              {editable("hero_primary_button", "Explore Our Services")} <span aria-hidden="true">→</span>
            </a>
            <a className="text-link" href="#about">{editable("hero_secondary_button", "View Company Profile")}</a>
          </div>
          <div className="trust-row" aria-label="Company strengths">
            <span>{editable("hero_trust_one", "Quality-Controlled")}</span>
            <span>{editable("hero_trust_two", "Reliable Delivery")}</span>
            <span>{editable("hero_trust_three", "Built to Specification")}</span>
          </div>
        </div>
        <a className="scroll-cue" href="#about" aria-label="Scroll to About Us">
          <span>{editable("hero_scroll_label", "Discover")}</span>
          <i aria-hidden="true" />
        </a>
      </section>

      <section className="about-section" id="about">
        <div className="section-number">01</div>
        <div className="section-heading">
          <p className="eyebrow">{editable("about_eyebrow", "About Techtonic")}</p>
          <h2>{headingLines(editable("about_heading", "Concrete confidence,\nfrom the ground up."))}</h2>
        </div>
        <div className="about-copy">
          <p>
            {editable("about_paragraph_one", "Established on September 3, 2020, Techtonic Concrete Industries Inc. supplies ready-mixed concrete for public and private projects throughout Bacolod City and Negros Occidental.")}
          </p>
          <p>
            {editable("about_paragraph_two", "From roads and bridges to malls and buildings, our computerized wet-mix batching plant and skilled team bring accuracy, consistency, and dependable service to every pour.")}
          </p>
          <div className="about-stats">
            <div><strong>{editable("about_stat_one_value", "90")}</strong><span>{editable("about_stat_one_label", "cu. m. hourly plant capacity")}</span></div>
            <div><strong>{editable("about_stat_two_value", "12")}</strong><span>{editable("about_stat_two_label", "transit mixers in the profile fleet")}</span></div>
            <div><strong>{editable("about_stat_three_value", "2020")}</strong><span>{editable("about_stat_three_label", "year established")}</span></div>
          </div>
        </div>
      </section>

      <section className="mission-section" id="mission">
        <div className="mission-intro">
          <p className="eyebrow">{editable("mission_eyebrow", "Mission & Vision")}</p>
          <h2>{headingLines(editable("mission_heading", "Measured by quality.\nDriven by service."))}</h2>
          <p>
            {editable("mission_intro", "Every batch, delivery, and customer relationship is guided by a clear standard: deliver dependable concrete with accuracy and care.")}
          </p>
        </div>
        <div className="mission-cards">
          <article>
            <span>{editable("mission_card_label", "Our Mission")}</span>
            <h3>{editable("mission_title", "Great service. Exceptional ready-mixed concrete.")}</h3>
            <p>
              {editable("mission_description", "To provide our customers with excellent service and produce high-quality ready-mixed concrete that meets their expectations.")}
            </p>
          </article>
          <article>
            <span>{editable("vision_card_label", "Our Vision")}</span>
            <h3>{editable("vision_title", "To lead through service, accuracy, and quality.")}</h3>
            <p>
              {editable("vision_description", "To be the top supplier of ready-mixed concrete, recognized for dependable service, precise production, and consistent quality.")}
            </p>
          </article>
        </div>
      </section>

      {SHOW_BUSINESS_REGISTRY && (
        <section className="registry-section" id="registry">
          <div className="section-topline">
            <div>
              <p className="eyebrow">{editable("registry_eyebrow", "Business Registry")}</p>
              <h2>{headingLines(editable("registry_heading", "Built on verified standards."))}</h2>
            </div>
            <p>
              {editable("registry_intro", "Registered, accredited, and supported by documented quality and calibration controls.")}
            </p>
          </div>
          <div className="registry-grid">
            {registryItems.map((item, index) => (
              <article key={item.title} className="registry-card">
                <div className="registry-image">
                  <img src={item.image} alt={`${item.title} document`} loading="lazy" />
                </div>
                <div>
                  <span>0{index + 1}</span>
                  <h3>{item.title}</h3>
                  <p>{item.note}</p>
                </div>
              </article>
            ))}
          </div>
        </section>
      )}

      <section className="team-section" id="team">
        <div className="section-topline">
          <div>
            <p className="eyebrow">{editable("team_eyebrow", "Our Team")}</p>
            <h2>{headingLines(editable("team_heading", "Experienced people.\nOne concrete standard."))}</h2>
          </div>
          <p>
            {editable("team_intro", "Leadership, technical oversight, and operational discipline working together on every project.")}
          </p>
        </div>
        <div className="leader-grid">
          {leaders.map((leader, index) => (
            <article className="leader-card" key={leader.name}>
              <div className="leader-mark">
                {leader.image ? (
                  <img src={leader.image} alt={leader.name} loading="lazy" />
                ) : (
                  <span>{leader.initials}</span>
                )}
                <i>0{index + 1}</i>
              </div>
              <h3>{leader.name}</h3>
              <p>{leader.role}</p>
            </article>
          ))}
        </div>
        <div className="board-list">
          <p>{editable("team_board_label", "Owners & Board")}</p>
          <div>
            {owners.map(([name, role]) => (
              <article key={name}>
                <h3>{name}</h3>
                <span>{role}</span>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section className="showcase-section facilities-section" id="facilities">
        <div className="section-topline">
          <div>
            <p className="eyebrow">{editable("facilities_eyebrow", "Our Facilities")}</p>
            <h2>{headingLines(editable("facilities_heading", "Purpose-built for precision."))}</h2>
          </div>
          <p>
            {editable("facilities_intro", "A connected production environment designed for accurate batching, controlled testing, and reliable supply.")}
          </p>
        </div>
        <div className="showcase-grid">
          {facilities.map((item, index) => (
            <article className={`showcase-card showcase-${index + 1}`} key={item.name}>
              <img src={item.image} alt={item.name} loading="lazy" />
              <div>
                <span>0{index + 1}</span>
                <h3>{item.name}</h3>
                <p>{item.detail}</p>
              </div>
            </article>
          ))}
        </div>
      </section>

      <section className="equipment-section" id="equipment">
        <div className="section-topline light">
          <div>
            <p className="eyebrow">{editable("equipment_eyebrow", "Our Equipment")}</p>
            <h2>{headingLines(editable("equipment_heading", "Capacity that keeps\nprojects moving."))}</h2>
          </div>
          <p>
            {editable("equipment_intro", "A coordinated fleet of transit mixers, pumps, and heavy equipment supports concrete delivery from plant to placement.")}
          </p>
        </div>
        <div className="equipment-grid">
          {equipment.map((item, index) => (
            <article key={item.name}>
              <div className="equipment-image">
                <img src={item.image} alt={item.name} loading="lazy" />
                <span>0{index + 1}</span>
              </div>
              <h3>{item.name}</h3>
              <p>{item.detail}</p>
            </article>
          ))}
        </div>
      </section>

      <section className="products-section" id="products">
        <div className="products-heading">
          <p className="eyebrow">{editable("products_eyebrow", "Our Products")}</p>
          <h2>{headingLines(editable("products_heading", "Concrete designed around the demands of the job."))}</h2>
        </div>
        <div className="product-list">
          {products.map((product, index) => (
            <article key={product.title}>
              <span>{String(index + 1).padStart(2, "0")}</span>
              <div>
                <h3>{product.title}</h3>
                <p>{product.description}</p>
              </div>
            </article>
          ))}
        </div>
        <a className="button button-primary product-cta" href="#contact">
          {editable("products_button_label", "Discuss Your Requirements")} <span aria-hidden="true">→</span>
        </a>
      </section>

      <section className="projects-section" aria-labelledby="projects-title">
        <div className="section-topline">
          <div>
            <p className="eyebrow">{editable("projects_eyebrow", "Selected Projects")}</p>
            <h2 id="projects-title">{headingLines(editable("projects_heading", "Proof in every pour."))}</h2>
          </div>
          <p>
            {editable("projects_intro", "Real project work featured in the Techtonic company profile across Bacolod City and Negros Occidental.")}
          </p>
        </div>
        <div className="project-grid">
          {projects.map((project) => (
            <article key={project.name}>
              <img src={project.image} alt={project.name} loading="lazy" />
              <div>
                <h3>{project.name}</h3>
                <span>{project.place}</span>
              </div>
            </article>
          ))}
        </div>
      </section>

      <section className="contact-section" id="contact">
        <div className="contact-main">
          <p className="eyebrow">{editable("contact_eyebrow", "Contact Us")}</p>
          <h2>{headingLines(editable("contact_heading", "Let's build something\nthat lasts."))}</h2>
          <p>
            {editable("contact_intro", "Tell us about your concrete requirements, schedule, and project location. Our team is ready to help.")}
          </p>
          <button className="button button-primary" type="button" onClick={() => setInquiryOpen(true)}>
            {editable("contact_button_label", "Send an Inquiry")} <span aria-hidden="true">→</span>
          </button>
        </div>
        <div className="contact-details">
          <div>
            <span>{editable("contact_address_label", "Office Address")}</span>
            <p>{contactAddress.map((line, index) => <span key={`${line}-${index}`}>{index > 0 && <br />}{line}</span>)}</p>
          </div>
          <div>
            <span>{editable("contact_telephone_label", "Telephone")}</span>
            <p>{contactTelephones.map((phone, index) => <span key={`${phone}-${index}`}>{index > 0 && <br />}<a href={phoneHref(phone)}>{phone}</a></span>)}</p>
          </div>
          <div>
            <span>{editable("contact_mobile_label", "Mobile")}</span>
            <p>{contactMobiles.map((phone, index) => <span key={`${phone}-${index}`}>{index > 0 && <br />}<a href={phoneHref(phone)}>{phone}</a></span>)}</p>
          </div>
          <div>
            <span>{editable("contact_email_label", "Email")}</span>
            <p><a href={`mailto:${contactEmail}`}>{contactEmail}</a></p>
          </div>
        </div>
      </section>

      {inquiryOpen && (
        <div className="inquiry-modal" role="dialog" aria-modal="true" aria-labelledby="inquiry-title">
          <button className="inquiry-backdrop" type="button" aria-label="Close inquiry form" onClick={() => setInquiryOpen(false)} />
          <div className="inquiry-panel">
            <button className="inquiry-close" type="button" aria-label="Close inquiry form" onClick={() => setInquiryOpen(false)}>×</button>
            <p className="eyebrow">{editable("inquiry_eyebrow", "Project Inquiry")}</p>
            <h2 id="inquiry-title">{headingLines(editable("inquiry_heading", "Tell us what\nyou're building."))}</h2>
            <form onSubmit={submitInquiry}>
              <div className="inquiry-row">
                <label>{editable("inquiry_name_label", "Name")}<input name="name" required maxLength={120} /></label>
                <label>{editable("inquiry_email_label", "Email")}<input type="email" name="email" required maxLength={190} /></label>
              </div>
              <div className="inquiry-row">
                <label>{editable("inquiry_phone_label", "Phone")}<input name="phone" maxLength={40} /></label>
                <label>{editable("inquiry_company_label", "Company")}<input name="company" maxLength={150} /></label>
              </div>
              <label>{editable("inquiry_subject_label", "Subject")}<input name="subject" maxLength={190} /></label>
              <label>{editable("inquiry_message_label", "Project requirements")}<textarea name="message" rows={5} required maxLength={5000} /></label>
              {formMessage && <p className="inquiry-message" aria-live="polite">{formMessage}</p>}
              <button className="button button-primary" type="submit" disabled={sending}>
                {sending ? editable("inquiry_sending_label", "Sending...") : editable("inquiry_submit_label", "Send Inquiry")} <span aria-hidden="true">→</span>
              </button>
            </form>
          </div>
        </div>
      )}

      <footer>
        <div className="footer-intro">
          <a className="footer-brand" href="#home" aria-label="Back to top" style={{ width: `${footerLogoWidth}px` }}>
            <img src={editable("footer_logo_image", "/images/techtonic-logo-white.png")} alt="Techtonic Concrete Industries Inc." />
          </a>
          <p>{editable("footer_tagline", "Reliable concrete. Built for what comes next.")}</p>
        </div>

        <div className="footer-links">
          <h2>{editable("footer_links_heading", "Quick Links")}</h2>
          <nav aria-label="Footer navigation">
            <a href="#about">{editable("nav_about", "About Us")}</a>
            <a href="#mission">{editable("nav_mission", "Mission & Vision")}</a>
            {SHOW_BUSINESS_REGISTRY && <a href="#registry">{editable("nav_registry", "Business Registry")}</a>}
            <a href="#team">{editable("nav_team", "Our Team")}</a>
            <a href="#facilities">{editable("nav_facilities", "Facilities")}</a>
            <a href="#equipment">{editable("nav_equipment", "Equipment")}</a>
            <a href="#products">{editable("nav_products", "Products")}</a>
            <a href="#contact">{editable("nav_contact", "Contact Us")}</a>
          </nav>
        </div>

        <div className="footer-contact">
          <h2>{editable("footer_contact_heading", "Get in Touch")}</h2>
          <a href={`mailto:${contactEmail}`}>{contactEmail}</a>
          {contactTelephones[0] && <a href={phoneHref(contactTelephones[0])}>{contactTelephones[0]}</a>}
          <p>{contactAddress.map((line, index) => <span key={`${line}-${index}`}>{index > 0 && <br />}{line}</span>)}</p>
        </div>

        <div className="footer-bottom">
          <span>© {new Date().getFullYear()} {editable("footer_company_name", "Techtonic Concrete Industries Inc.")}</span>
          <a href="#home">{editable("footer_back_to_top", "Back to top")} ↑</a>
        </div>
      </footer>
    </main>
  );
}
