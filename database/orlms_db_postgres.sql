--
-- PostgreSQL database dump
--
-- (Removed \restrict)

-- Dumped from database version 16.14
-- Dumped by pg_dump version 16.14

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: amendment_status_enum; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.amendment_status_enum AS ENUM (
    'draft',
    'submitted',
    'approved',
    'rejected'
);


ALTER TYPE public.amendment_status_enum OWNER TO postgres;

--
-- Name: doc_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.doc_status AS ENUM (
    'draft',
    'submitted',
    'under_review',
    'endorsed',
    'approved',
    'enacted',
    'rejected',
    'archived',
    'published',
    'certified',
    'signed_lce',
    'vetoed',
    'sp_review_approved',
    'sp_review_disapproved',
    'sp_review_comments'
);


ALTER TYPE public.doc_status OWNER TO postgres;

--
-- Name: document_type_enum; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.document_type_enum AS ENUM (
    'ordinance',
    'resolution'
);


ALTER TYPE public.document_type_enum OWNER TO postgres;

--
-- Name: implementation_status_enum; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.implementation_status_enum AS ENUM (
    'pending',
    'ongoing',
    'completed',
    'delayed'
);


ALTER TYPE public.implementation_status_enum OWNER TO postgres;

--
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.user_role AS ENUM (
    'super_admin',
    'legislative_staff',
    'committee_member',
    'sp_member'
);


ALTER TYPE public.user_role OWNER TO postgres;

--
-- Name: validation_status_enum; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.validation_status_enum AS ENUM (
    'passed',
    'flagged',
    'failed'
);


ALTER TYPE public.validation_status_enum OWNER TO postgres;

--
-- Name: set_updated_at(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.set_updated_at() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.set_updated_at() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: ai_validation_reports; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ai_validation_reports (
    id integer NOT NULL,
    document_type public.document_type_enum NOT NULL,
    document_id integer NOT NULL,
    validation_status public.validation_status_enum DEFAULT 'flagged'::public.validation_status_enum NOT NULL,
    completeness_score integer DEFAULT 0 NOT NULL,
    similarity_score real DEFAULT 0 NOT NULL,
    similar_document_type public.document_type_enum,
    similar_document_id integer,
    similar_document_no character varying(50) DEFAULT NULL::character varying,
    completeness_details jsonb,
    similarity_details jsonb,
    ai_summary text,
    recommendation text,
    raw_response text,
    validated_by integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.ai_validation_reports OWNER TO postgres;

--
-- Name: ai_validation_reports_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ai_validation_reports_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ai_validation_reports_id_seq OWNER TO postgres;

--
-- Name: ai_validation_reports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ai_validation_reports_id_seq OWNED BY public.ai_validation_reports.id;


--
-- Name: amendments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.amendments (
    id integer NOT NULL,
    document_type public.document_type_enum NOT NULL,
    document_id integer NOT NULL,
    amendment_no character varying(100) NOT NULL,
    description text NOT NULL,
    changes text NOT NULL,
    status public.amendment_status_enum DEFAULT 'draft'::public.amendment_status_enum NOT NULL,
    amended_by integer,
    amended_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.amendments OWNER TO postgres;

--
-- Name: amendments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.amendments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.amendments_id_seq OWNER TO postgres;

--
-- Name: amendments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.amendments_id_seq OWNED BY public.amendments.id;


--
-- Name: audit_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.audit_logs (
    id integer NOT NULL,
    user_id integer,
    action character varying(100) NOT NULL,
    table_name character varying(100) NOT NULL,
    record_id integer,
    old_value text,
    new_value text,
    ip_address character varying(45) DEFAULT NULL::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.audit_logs OWNER TO postgres;

--
-- Name: audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.audit_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.audit_logs_id_seq OWNER TO postgres;

--
-- Name: audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.audit_logs_id_seq OWNED BY public.audit_logs.id;


--
-- Name: committees; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.committees (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    jurisdiction text NOT NULL,
    chairperson_id integer,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.committees OWNER TO postgres;

--
-- Name: committees_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.committees_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.committees_id_seq OWNER TO postgres;

--
-- Name: committees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.committees_id_seq OWNED BY public.committees.id;


--
-- Name: monitoring_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.monitoring_logs (
    id integer NOT NULL,
    document_type public.document_type_enum NOT NULL,
    document_id integer NOT NULL,
    implementation_status public.implementation_status_enum NOT NULL,
    implementation_notes text NOT NULL,
    logged_by integer,
    logged_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.monitoring_logs OWNER TO postgres;

--
-- Name: monitoring_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.monitoring_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.monitoring_logs_id_seq OWNER TO postgres;

--
-- Name: monitoring_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.monitoring_logs_id_seq OWNED BY public.monitoring_logs.id;


--
-- Name: ordinances; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ordinances (
    id integer NOT NULL,
    ordinance_no character varying(50) NOT NULL,
    title character varying(500) NOT NULL,
    subject character varying(500) DEFAULT NULL::character varying,
    content text,
    author_id integer,
    committee_id integer,
    status public.doc_status DEFAULT 'draft'::public.doc_status NOT NULL,
    ai_summary text,
    file_path character varying(500) DEFAULT NULL::character varying,
    date_filed date,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.ordinances OWNER TO postgres;

--
-- Name: ordinances_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ordinances_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ordinances_id_seq OWNER TO postgres;

--
-- Name: ordinances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ordinances_id_seq OWNED BY public.ordinances.id;


--
-- Name: public_consultations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.public_consultations (
    id integer NOT NULL,
    document_id integer NOT NULL,
    document_type character varying(20) NOT NULL,
    hearing_date date NOT NULL,
    venue character varying(255) NOT NULL,
    total_participants integer DEFAULT 0,
    total_opinions integer DEFAULT 0,
    sentiment_summary character varying(100),
    summary_report text,
    report_file_url character varying(500),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT public_consultations_document_type_check CHECK (((document_type)::text = ANY ((ARRAY['ordinance'::character varying, 'resolution'::character varying])::text[])))
);


ALTER TABLE public.public_consultations OWNER TO postgres;

--
-- Name: public_consultations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.public_consultations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.public_consultations_id_seq OWNER TO postgres;

--
-- Name: public_consultations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.public_consultations_id_seq OWNED BY public.public_consultations.id;


--
-- Name: publications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.publications (
    id integer NOT NULL,
    document_type public.document_type_enum NOT NULL,
    document_id integer NOT NULL,
    publication_ref character varying(255) NOT NULL,
    plain_summary text NOT NULL,
    file_path character varying(500) DEFAULT NULL::character varying,
    published_by integer,
    published_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.publications OWNER TO postgres;

--
-- Name: publications_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.publications_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.publications_id_seq OWNER TO postgres;

--
-- Name: publications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.publications_id_seq OWNED BY public.publications.id;


--
-- Name: resolutions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.resolutions (
    id integer NOT NULL,
    resolution_no character varying(50) NOT NULL,
    title character varying(500) NOT NULL,
    subject character varying(500) DEFAULT NULL::character varying,
    content text,
    author_id integer,
    committee_id integer,
    status public.doc_status DEFAULT 'draft'::public.doc_status NOT NULL,
    ai_summary text,
    file_path character varying(500) DEFAULT NULL::character varying,
    date_filed date,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.resolutions OWNER TO postgres;

--
-- Name: resolutions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.resolutions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.resolutions_id_seq OWNER TO postgres;

--
-- Name: resolutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.resolutions_id_seq OWNED BY public.resolutions.id;


--
-- Name: review_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.review_logs (
    id integer NOT NULL,
    document_type public.document_type_enum NOT NULL,
    document_id integer NOT NULL,
    action character varying(100) NOT NULL,
    reason text,
    reviewed_by integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.review_logs OWNER TO postgres;

--
-- Name: review_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.review_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.review_logs_id_seq OWNER TO postgres;

--
-- Name: review_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.review_logs_id_seq OWNED BY public.review_logs.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    name character varying(150) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    role public.user_role DEFAULT 'legislative_staff'::public.user_role NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: ai_validation_reports id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ai_validation_reports ALTER COLUMN id SET DEFAULT nextval('public.ai_validation_reports_id_seq'::regclass);


--
-- Name: amendments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.amendments ALTER COLUMN id SET DEFAULT nextval('public.amendments_id_seq'::regclass);


--
-- Name: audit_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs ALTER COLUMN id SET DEFAULT nextval('public.audit_logs_id_seq'::regclass);


--
-- Name: committees id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.committees ALTER COLUMN id SET DEFAULT nextval('public.committees_id_seq'::regclass);


--
-- Name: monitoring_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monitoring_logs ALTER COLUMN id SET DEFAULT nextval('public.monitoring_logs_id_seq'::regclass);


--
-- Name: ordinances id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordinances ALTER COLUMN id SET DEFAULT nextval('public.ordinances_id_seq'::regclass);


--
-- Name: public_consultations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.public_consultations ALTER COLUMN id SET DEFAULT nextval('public.public_consultations_id_seq'::regclass);


--
-- Name: publications id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publications ALTER COLUMN id SET DEFAULT nextval('public.publications_id_seq'::regclass);


--
-- Name: resolutions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resolutions ALTER COLUMN id SET DEFAULT nextval('public.resolutions_id_seq'::regclass);


--
-- Name: review_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review_logs ALTER COLUMN id SET DEFAULT nextval('public.review_logs_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: ai_validation_reports ai_validation_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ai_validation_reports
    ADD CONSTRAINT ai_validation_reports_pkey PRIMARY KEY (id);


--
-- Name: amendments amendments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.amendments
    ADD CONSTRAINT amendments_pkey PRIMARY KEY (id);


--
-- Name: audit_logs audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_pkey PRIMARY KEY (id);


--
-- Name: committees committees_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.committees
    ADD CONSTRAINT committees_pkey PRIMARY KEY (id);


--
-- Name: monitoring_logs monitoring_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monitoring_logs
    ADD CONSTRAINT monitoring_logs_pkey PRIMARY KEY (id);


--
-- Name: ordinances ordinances_ordinance_no_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordinances
    ADD CONSTRAINT ordinances_ordinance_no_key UNIQUE (ordinance_no);


--
-- Name: ordinances ordinances_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordinances
    ADD CONSTRAINT ordinances_pkey PRIMARY KEY (id);


--
-- Name: public_consultations public_consultations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.public_consultations
    ADD CONSTRAINT public_consultations_pkey PRIMARY KEY (id);


--
-- Name: publications publications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publications
    ADD CONSTRAINT publications_pkey PRIMARY KEY (id);


--
-- Name: resolutions resolutions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resolutions
    ADD CONSTRAINT resolutions_pkey PRIMARY KEY (id);


--
-- Name: resolutions resolutions_resolution_no_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resolutions
    ADD CONSTRAINT resolutions_resolution_no_key UNIQUE (resolution_no);


--
-- Name: review_logs review_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review_logs
    ADD CONSTRAINT review_logs_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: idx_aivr_document; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_aivr_document ON public.ai_validation_reports USING btree (document_type, document_id);


--
-- Name: idx_aivr_validator; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_aivr_validator ON public.ai_validation_reports USING btree (validated_by);


--
-- Name: idx_amend_document; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_amend_document ON public.amendments USING btree (document_type, document_id);


--
-- Name: idx_amend_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_amend_user ON public.amendments USING btree (amended_by);


--
-- Name: idx_audit_action; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_audit_action ON public.audit_logs USING btree (action);


--
-- Name: idx_audit_table; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_audit_table ON public.audit_logs USING btree (table_name);


--
-- Name: idx_audit_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_audit_user ON public.audit_logs USING btree (user_id);


--
-- Name: idx_committees_chairperson; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_committees_chairperson ON public.committees USING btree (chairperson_id);


--
-- Name: idx_consultations_document; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_consultations_document ON public.public_consultations USING btree (document_type, document_id);


--
-- Name: idx_ml_document; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ml_document ON public.monitoring_logs USING btree (document_type, document_id);


--
-- Name: idx_ml_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ml_user ON public.monitoring_logs USING btree (logged_by);


--
-- Name: idx_ordinances_author; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ordinances_author ON public.ordinances USING btree (author_id);


--
-- Name: idx_ordinances_committee; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ordinances_committee ON public.ordinances USING btree (committee_id);


--
-- Name: idx_ordinances_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_ordinances_status ON public.ordinances USING btree (status);


--
-- Name: idx_pub_document; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pub_document ON public.publications USING btree (document_type, document_id);


--
-- Name: idx_pub_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pub_user ON public.publications USING btree (published_by);


--
-- Name: idx_resolutions_author; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_resolutions_author ON public.resolutions USING btree (author_id);


--
-- Name: idx_resolutions_committee; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_resolutions_committee ON public.resolutions USING btree (committee_id);


--
-- Name: idx_resolutions_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_resolutions_status ON public.resolutions USING btree (status);


--
-- Name: idx_rl_document; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rl_document ON public.review_logs USING btree (document_type, document_id);


--
-- Name: idx_rl_reviewer; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_rl_reviewer ON public.review_logs USING btree (reviewed_by);


--
-- Name: ordinances trg_ordinances_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_ordinances_updated_at BEFORE UPDATE ON public.ordinances FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();


--
-- Name: resolutions trg_resolutions_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_resolutions_updated_at BEFORE UPDATE ON public.resolutions FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();


--
-- Name: ai_validation_reports ai_validation_reports_validated_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ai_validation_reports
    ADD CONSTRAINT ai_validation_reports_validated_by_fkey FOREIGN KEY (validated_by) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: amendments amendments_amended_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.amendments
    ADD CONSTRAINT amendments_amended_by_fkey FOREIGN KEY (amended_by) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: audit_logs audit_logs_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: committees committees_chairperson_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.committees
    ADD CONSTRAINT committees_chairperson_id_fkey FOREIGN KEY (chairperson_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: monitoring_logs monitoring_logs_logged_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monitoring_logs
    ADD CONSTRAINT monitoring_logs_logged_by_fkey FOREIGN KEY (logged_by) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: ordinances ordinances_author_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordinances
    ADD CONSTRAINT ordinances_author_id_fkey FOREIGN KEY (author_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: ordinances ordinances_committee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordinances
    ADD CONSTRAINT ordinances_committee_id_fkey FOREIGN KEY (committee_id) REFERENCES public.committees(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: publications publications_published_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publications
    ADD CONSTRAINT publications_published_by_fkey FOREIGN KEY (published_by) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: resolutions resolutions_author_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resolutions
    ADD CONSTRAINT resolutions_author_id_fkey FOREIGN KEY (author_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: resolutions resolutions_committee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.resolutions
    ADD CONSTRAINT resolutions_committee_id_fkey FOREIGN KEY (committee_id) REFERENCES public.committees(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: review_logs review_logs_reviewed_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review_logs
    ADD CONSTRAINT review_logs_reviewed_by_fkey FOREIGN KEY (reviewed_by) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Default Admin Account Insert
--
INSERT INTO public.users (name, email, password, role, is_active)
VALUES (
    'Administrator',
    'admin@orlms.ph',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'super_admin',
    true
) ON CONFLICT (email) DO NOTHING;


--
-- PostgreSQL database dump complete
--
-- (Removed \unrestrict)

