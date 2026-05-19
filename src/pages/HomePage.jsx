import Hero from "../components/Hero";
import Services from "../components/Services";
import Formations from "../components/Formations";
import Projects from "../components/Projects";
import WhyUs from "../components/WhyUs";
import Blog from "../components/Blog";
import Boutique from "../components/Boutique";
import Testimonials from "../components/Testimonials";
import CTA from "../components/CTA";

export default function HomePage() {
  return (
    <>
      <Hero />
      <Services />
      <Projects />
      <Formations />
      <WhyUs />
      <Blog />
      <Boutique />
      <Testimonials />
      <CTA />
    </>
  );
}
