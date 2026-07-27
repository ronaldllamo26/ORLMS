# Ordinance and Resolution Lifecycle Workflow

Narito ang visual na representasyon kung paano gumagalaw ang data papasok, sa loob, at palabas ng inyong system (**Ordinance and Resolution Lifecycle Management System**).

```mermaid
graph TD
    classDef incoming fill:#e3f2fd,stroke:#1e88e5,stroke-width:2px;
    classDef core fill:#e8f5e9,stroke:#43a047,stroke-width:2px;
    classDef outgoing fill:#f3e5f5,stroke:#8e24aa,stroke-width:2px;

    subgraph INCOMING["Sino ang mga 'Bumabato' sa inyo? (Inputs)"]
        CE[Citizen Engagement System]:::incoming
        LR[Legislative Research System]:::incoming
        REC_IN[Records System / Archives]:::incoming
        COM[Committee Management System]:::incoming
    end

    subgraph CORE["Ang System Ninyo (Lifecycle Management)"]
        DRAFT(Drafting & Encoding Module):::core
        AMEND(Amendment & Revision Tracking Module):::core
        REVIEW(Review & Committee Endorsement Module):::core
        APPROVE(Approval & Enactment Module):::core
    end

    subgraph OUTGOING["Sino ang mga 'Binabato' ninyo? (Outputs)"]
        SESS[Session Management System]:::outgoing
        VOTE[Voting & Decision Support System]:::outgoing
        REC_OUT[Records System / Archives]:::outgoing
    end

    %% Flow logic - INCOMING to CORE
    CE -.-> |Proposals / Complaints| DRAFT
    LR -.-> |Policy Briefs| DRAFT
    REC_IN -.-> |Old Documents (para baguhin)| AMEND
    
    %% Committee is a special case, they receive the draft then return a report
    REVIEW -.-> |Ipapadala for Committee Hearing| COM
    COM -.-> |Ibabato pabalik ang Committee Report| REVIEW

    %% CORE Logic
    DRAFT ===> |For Checking| REVIEW
    AMEND ===> |For Checking| REVIEW

    %% CORE to OUTGOING
    REVIEW ===> |Approved by Committee| SESS
    SESS -.-> |Babasahin sa Session| VOTE
    VOTE -.-> |Resulta ng Botohan| APPROVE
    APPROVE ===> |Na-enact na (Final Document)| REC_OUT
```

### Paano Basahin ang Diagram:

1. **Blue Boxes (Inputs):** Sila yung mga system na magpapasa ("mambabato") ng documents sa inyo.
   * *Citizen & Research* = Magpapasa ng ideas na gagawin niyong draft.
   * *Records System* = Magpapasa ng lumang batas na babaguhin (a-amend) ninyo.
   * *Committee* = Magpapasa ng report pagkatapos nilang pag-aralan ang isang draft.

2. **Green Boxes (Kayo Ito!):** Dito nangyayari ang trabaho ng system ninyo.
   * Tumatanggap kayo ng ideas para i-**Draft**.
   * Tumatanggap kayo ng lumang batas para i-**Amend**.
   * Tumatanggap kayo ng reports para i-**Review / Check**.

3. **Purple Boxes (Outputs):** Kapag na-check niyo na ng maayos, kayo naman ang magpapasa palabas:
   * Ipapasa sa **Session** para i-schedule.
   * Ipapasa sa **Voting** para botohan ng mga konsehal.
   * Ipapasa sa **Records** para maitago (archive) kapag naging pormal na batas na.
