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
  }
}

const cms = window.__TECHTONIC_CONTENT__ ?? {};

const serviceLinks = [
  { label: "Facilities", href: "#facilities" },
  { label: "Equipment", href: "#equipment" },
  { label: "Products", href: "#products" },
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
      if (!response.ok) throw new Error(data.message || "Please check your details and try again.");
      form.reset();
      setFormMessage(data.message);
    } catch (error) {
      setFormMessage(error instanceof Error ? error.message : "Unable to send your inquiry.");
    } finally {
      setSending(false);
    }
  };

  return (
    <main>
      <header className={`site-header ${scrolled ? "is-scrolled" : ""}`}>
        <a className="brand" href="#home" aria-label="Techtonic home">
          <img src="/images/techtonic-logo-white.png" alt="Techtonic Concrete Industries Inc." />
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
          <span className="mobile-menu-label">Navigation</span>
          <a href="#about" onClick={closeMenus}>About Us</a>
          <a href="#mission" onClick={closeMenus}>Mission &amp; Vision</a>
          {SHOW_BUSINESS_REGISTRY && (
            <a href="#registry" onClick={closeMenus}>Business Registry</a>
          )}
          <a href="#team" onClick={closeMenus}>Our Team</a>
          <div className={`services-menu ${servicesOpen ? "is-open" : ""}`}>
            <button
              type="button"
              aria-expanded={servicesOpen}
              aria-haspopup="true"
              onClick={() => setServicesOpen((open) => !open)}
            >
              Services <span className="chevron" aria-hidden="true" />
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
          <a className="nav-contact" href="#contact" onClick={closeMenus}>Contact Us</a>
        </nav>
      </header>

      <section className="hero" id="home">
        <div className="hero-media" aria-hidden="true" />
        <div className="hero-shade" aria-hidden="true" />
        <div className="hero-content">
          <p className="eyebrow">Built for what comes next</p>
          <h1>Engineering Strength.<br />Delivering Certainty.</h1>
          <p className="hero-copy">
            Reliable ready-mixed concrete, engineered for enduring projects
            across Bacolod City and Negros Occidental.
          </p>
          <div className="hero-actions">
            <a className="button button-primary" href="#facilities">
              Explore Our Services <span aria-hidden="true">→</span>
            </a>
            <a className="text-link" href="#about">View Company Profile</a>
          </div>
          <div className="trust-row" aria-label="Company strengths">
            <span>Quality-Controlled</span>
            <span>Reliable Delivery</span>
            <span>Built to Specification</span>
          </div>
        </div>
        <a className="scroll-cue" href="#about" aria-label="Scroll to About Us">
          <span>Discover</span>
          <i aria-hidden="true" />
        </a>
      </section>

      <section className="about-section" id="about">
        <div className="section-number">01</div>
        <div className="section-heading">
          <p className="eyebrow">About Techtonic</p>
          <h2>Concrete confidence,<br />from the ground up.</h2>
        </div>
        <div className="about-copy">
          <p>
            Established on September 3, 2020, Techtonic Concrete Industries Inc.
            supplies ready-mixed concrete for public and private projects throughout
            Bacolod City and Negros Occidental.
          </p>
          <p>
            From roads and bridges to malls and buildings, our computerized wet-mix
            batching plant and skilled team bring accuracy, consistency, and dependable
            service to every pour.
          </p>
          <div className="about-stats">
            <div><strong>90</strong><span>cu. m. hourly plant capacity</span></div>
            <div><strong>12</strong><span>transit mixers in the profile fleet</span></div>
            <div><strong>2020</strong><span>year established</span></div>
          </div>
        </div>
      </section>

      <section className="mission-section" id="mission">
        <div className="mission-intro">
          <p className="eyebrow">Mission &amp; Vision</p>
          <h2>Measured by quality.<br />Driven by service.</h2>
          <p>
            Every batch, delivery, and customer relationship is guided by a clear
            standard: deliver dependable concrete with accuracy and care.
          </p>
        </div>
        <div className="mission-cards">
          <article>
            <span>Our Mission</span>
            <h3>Great service. Exceptional ready-mixed concrete.</h3>
            <p>
              To provide our customers with excellent service and produce high-quality
              ready-mixed concrete that meets their expectations.
            </p>
          </article>
          <article>
            <span>Our Vision</span>
            <h3>To lead through service, accuracy, and quality.</h3>
            <p>
              To be the top supplier of ready-mixed concrete, recognized for dependable
              service, precise production, and consistent quality.
            </p>
          </article>
        </div>
      </section>

      {SHOW_BUSINESS_REGISTRY && (
        <section className="registry-section" id="registry">
          <div className="section-topline">
            <div>
              <p className="eyebrow">Business Registry</p>
              <h2>Built on verified standards.</h2>
            </div>
            <p>
              Registered, accredited, and supported by documented quality and calibration
              controls.
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
            <p className="eyebrow">Our Team</p>
            <h2>Experienced people.<br />One concrete standard.</h2>
          </div>
          <p>
            Leadership, technical oversight, and operational discipline working
            together on every project.
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
          <p>Owners &amp; Board</p>
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
            <p className="eyebrow">Our Facilities</p>
            <h2>Purpose-built for precision.</h2>
          </div>
          <p>
            A connected production environment designed for accurate batching,
            controlled testing, and reliable supply.
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
            <p className="eyebrow">Our Equipment</p>
            <h2>Capacity that keeps<br />projects moving.</h2>
          </div>
          <p>
            A coordinated fleet of transit mixers, pumps, and heavy equipment supports
            concrete delivery from plant to placement.
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
          <p className="eyebrow">Our Products</p>
          <h2>Concrete designed around the demands of the job.</h2>
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
          Discuss Your Requirements <span aria-hidden="true">→</span>
        </a>
      </section>

      <section className="projects-section" aria-labelledby="projects-title">
        <div className="section-topline">
          <div>
            <p className="eyebrow">Selected Projects</p>
            <h2 id="projects-title">Proof in every pour.</h2>
          </div>
          <p>
            Real project work featured in the Techtonic company profile across Bacolod
            City and Negros Occidental.
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
          <p className="eyebrow">Contact Us</p>
          <h2>Let&apos;s build something<br />that lasts.</h2>
          <p>
            Tell us about your concrete requirements, schedule, and project location.
            Our team is ready to help.
          </p>
          <button className="button button-primary" type="button" onClick={() => setInquiryOpen(true)}>
            Send an Inquiry <span aria-hidden="true">→</span>
          </button>
        </div>
        <div className="contact-details">
          <div>
            <span>Office Address</span>
            <p>Purok Paho, Brgy. Felisa<br />Bacolod City, Negros Occidental<br />Philippines 6100</p>
          </div>
          <div>
            <span>Telephone</span>
            <p><a href="tel:+63342130490">034-213-0490</a><br /><a href="tel:+63344619194">034-461-9194</a></p>
          </div>
          <div>
            <span>Mobile</span>
            <p>
              <a href="tel:+639984762210">0998-476-2210</a><br />
              <a href="tel:+639186640085">0918-664-0085</a><br />
              <a href="tel:+639369233732">0936-923-3732</a>
            </p>
          </div>
          <div>
            <span>Email</span>
            <p><a href="mailto:techtonicrmc@gmail.com">techtonicrmc@gmail.com</a></p>
          </div>
        </div>
      </section>

      {inquiryOpen && (
        <div className="inquiry-modal" role="dialog" aria-modal="true" aria-labelledby="inquiry-title">
          <button className="inquiry-backdrop" type="button" aria-label="Close inquiry form" onClick={() => setInquiryOpen(false)} />
          <div className="inquiry-panel">
            <button className="inquiry-close" type="button" aria-label="Close inquiry form" onClick={() => setInquiryOpen(false)}>×</button>
            <p className="eyebrow">Project Inquiry</p>
            <h2 id="inquiry-title">Tell us what<br />you&apos;re building.</h2>
            <form onSubmit={submitInquiry}>
              <div className="inquiry-row">
                <label>Name<input name="name" required maxLength={120} /></label>
                <label>Email<input type="email" name="email" required maxLength={190} /></label>
              </div>
              <div className="inquiry-row">
                <label>Phone<input name="phone" maxLength={40} /></label>
                <label>Company<input name="company" maxLength={150} /></label>
              </div>
              <label>Subject<input name="subject" maxLength={190} /></label>
              <label>Project requirements<textarea name="message" rows={5} required maxLength={5000} /></label>
              {formMessage && <p className="inquiry-message" aria-live="polite">{formMessage}</p>}
              <button className="button button-primary" type="submit" disabled={sending}>
                {sending ? "Sending..." : "Send Inquiry"} <span aria-hidden="true">→</span>
              </button>
            </form>
          </div>
        </div>
      )}

      <footer>
        <div className="footer-intro">
          <a className="footer-brand" href="#home" aria-label="Back to top">
            <img src="/images/techtonic-logo-white.png" alt="Techtonic Concrete Industries Inc." />
          </a>
          <p>Reliable concrete. Built for what comes next.</p>
        </div>

        <div className="footer-links">
          <h2>Quick Links</h2>
          <nav aria-label="Footer navigation">
            <a href="#about">About Us</a>
            <a href="#mission">Mission &amp; Vision</a>
            {SHOW_BUSINESS_REGISTRY && <a href="#registry">Business Registry</a>}
            <a href="#team">Our Team</a>
            <a href="#facilities">Facilities</a>
            <a href="#equipment">Equipment</a>
            <a href="#products">Products</a>
            <a href="#contact">Contact Us</a>
          </nav>
        </div>

        <div className="footer-contact">
          <h2>Get in Touch</h2>
          <a href="mailto:techtonicrmc@gmail.com">techtonicrmc@gmail.com</a>
          <a href="tel:+63342130490">034-213-0490</a>
          <p>Purok Paho, Brgy. Felisa<br />Bacolod City, Negros Occidental</p>
        </div>

        <div className="footer-bottom">
          <span>© {new Date().getFullYear()} Techtonic Concrete Industries Inc.</span>
          <a href="#home">Back to top ↑</a>
        </div>
      </footer>
    </main>
  );
}
